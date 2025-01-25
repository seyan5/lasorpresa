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
            // Fetch container and flower details, calculate total price...

            // Insert expected image into `custom_images`
            if (!empty($customization['expected_image'])) {
                $stmt = $pdo->prepare("
                    INSERT INTO custom_images (order_id, expected_image) 
                    VALUES (:order_id, :expected_image)
                ");
                $stmt->execute([
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
            'amount_paid' => $amount_paid, // Use POST amount paid for both payment methods
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