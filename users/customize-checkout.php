<?php
require("conn.php");

// Check if the user is logged in
if (!isset($_SESSION['customer']['cust_id'])) {
    echo "You need to log in to proceed to checkout.";
    exit;
}

// Fetch customer details
$customer_id = $_SESSION['customer']['cust_id'];
$stmt = $pdo->prepare("SELECT cust_name, cust_email, cust_address FROM customer WHERE cust_id = :cust_id");
$stmt->execute(['cust_id' => $customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "Customer details not found.";
    exit;
}

// Check if customization session data exists
if (!isset($_SESSION['customization']) || empty($_SESSION['customization'])) {
    echo "No customizations found in the session. Please go back to the cart.";
    exit;
}

$grouped_customization = $_SESSION['customization'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_customizations'])) {
    $selected_indexes = json_decode($_POST['selected_customizations'], true);

    if (!is_array($selected_indexes) || empty($selected_indexes)) {
        echo "No customizations selected. Please go back to the cart.";
        exit;
    }

    $amount_paid = $_POST['amount_paid'] ?? 0; // Fetch amount paid from POST
    $payment_method = $_POST['payment_method'] ?? 'cop';
    $reference_number = $_POST['reference_number'] ?? 'N/A'; // Default to 'N/A' for COP

    $selected_customizations = array_intersect_key($grouped_customization, array_flip($selected_indexes));
    $remaining_customizations = array_diff_key($grouped_customization, $selected_customizations);

    try {
        $pdo->beginTransaction();

        // Insert into `custom_order`
        $stmt = $pdo->prepare("
            INSERT INTO custom_order (cust_id, customer_name, customer_email, shipping_address, total_price, order_date)
            VALUES (:cust_id, :customer_name, :customer_email, :shipping_address, :total_price, NOW())
        ");
        $stmt->execute([
            'cust_id' => $customer_id,
            'customer_name' => $customer['cust_name'],
            'customer_email' => $customer['cust_email'],
            'shipping_address' => $customer['cust_address'],
            'total_price' => 0, // Placeholder, updated later
        ]);
        $order_id = $pdo->lastInsertId();

        $total_price = 0;

        foreach ($selected_customizations as $customization) {
            // Fetch container and flower details, calculate total price
            $stmt = $pdo->prepare("SELECT container_name, price FROM container WHERE container_id = :container_id");
            $stmt->execute(['container_id' => $customization['container_type']]);
            $container = $stmt->fetch(PDO::FETCH_ASSOC);

            $container_name = $container['container_name'] ?? 'Unknown';
            $container_price = $container['price'] ?? 0;
            $customization_total_price = $container_price;

            $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = :color_id");
            $stmt->execute(['color_id' => $customization['container_color']]);
            $color = $stmt->fetch(PDO::FETCH_ASSOC);
            $color_name = $color['color_name'] ?? 'Unknown';

            $flower_details = [];
            foreach ($customization['flowers'] as $flower) {
                $stmt = $pdo->prepare("SELECT name, price, quantity FROM flowers WHERE id = :flower_id");
                $stmt->execute(['flower_id' => $flower['flower_type']]);
                $flower_data = $stmt->fetch(PDO::FETCH_ASSOC);

                $flower_name = $flower_data['name'] ?? 'Unknown';
                $flower_price = $flower_data['price'] ?? 0;
                $num_flowers = $flower['num_flowers'] ?? 0;

                $flower_total_price = $flower_price * $num_flowers;
                $customization_total_price += $flower_total_price;

                $flower_details[] = "{$num_flowers}x {$flower_name} (₱{$flower_total_price})";

                // Deduct the ordered quantity from the flowers table
                if ($flower_data) {
                    $new_quantity = $flower_data['quantity'] - $num_flowers;
                    if ($new_quantity < 0) {
                        throw new Exception("Insufficient stock for flower: {$flower_name}");
                    }

                    $stmt = $pdo->prepare("UPDATE flowers SET quantity = :new_quantity WHERE id = :flower_id");
                    $stmt->execute(['new_quantity' => $new_quantity, 'flower_id' => $flower['flower_type']]);
                }
            }

            $flower_details_string = implode(", ", $flower_details);

            // Insert into `custom_orderitems` (with remarks)
            $stmt = $pdo->prepare("
                INSERT INTO custom_orderitems (order_id, flower_details, container_type, container_color, flower_price, container_price, total_price, remarks)
                VALUES (:order_id, :flower_details, :container_type, :container_color, :flower_price, :container_price, :total_price, :remarks)
            ");
            $stmt->execute([
                'order_id' => $order_id,
                'flower_details' => $flower_details_string,
                'container_type' => $container_name,
                'container_color' => $color_name,
                'flower_price' => $customization_total_price - $container_price,
                'container_price' => $container_price,
                'total_price' => $customization_total_price,
                'remarks' => $customization['remarks'] ?? 'None',
            ]);

            $orderitem_id = $pdo->lastInsertId(); // Capture the last inserted `orderitem_id`
            $total_price += $customization_total_price;

            // Insert expected image into `custom_images`
            if (!empty($customization['expected_image'])) {
                $stmt = $pdo->prepare("
                    INSERT INTO custom_images (orderitem_id, order_id, expected_image) 
                    VALUES (:orderitem_id, :order_id, :expected_image)
                ");
                $stmt->execute([
                    'orderitem_id' => $orderitem_id,
                    'order_id' => $order_id,
                    'expected_image' => $customization['expected_image']
                ]);
            }
        }

        // Update total price in `custom_order`
        $stmt = $pdo->prepare("UPDATE custom_order SET total_price = :total_price WHERE order_id = :order_id");
        $stmt->execute(['total_price' => $total_price, 'order_id' => $order_id]);

        // Insert into `custom_payment`
        $stmt = $pdo->prepare("
            INSERT INTO custom_payment (order_id, customer_name, customer_email, reference_number, amount_paid, payment_method, payment_status, shipping_status, order_date)
            VALUES (:order_id, :customer_name, :customer_email, :reference_number, :amount_paid, :payment_method, :payment_status, :shipping_status, NOW())
        ");
        $stmt->execute([
            'order_id' => $order_id,
            'customer_name' => $customer['cust_name'],
            'customer_email' => $customer['cust_email'],
            'reference_number' => $reference_number,
            'amount_paid' => $amount_paid,
            'payment_method' => $payment_method,
            'payment_status' => $payment_method === 'gcash' ? 'Paid' : 'Pending',
            'shipping_status' => 'Pending',
        ]);

        $pdo->commit();

        $_SESSION['customization'] = $remaining_customizations;

        echo "<script>alert('Checkout successful!');</script>";
        echo "<script>window.location.href = 'customization.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to process the order: " . $e->getMessage();
    }
} else {
    echo "No customizations selected. Please go back to the cart.";
}
?>
