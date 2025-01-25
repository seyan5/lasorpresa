<?php
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");

// Ensure user is logged in
if (!isset($_SESSION['customer'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$cust_email = $_SESSION['customer']['cust_email'] ?? null;
$cust_id = $_SESSION['customer']['cust_id'] ?? null;

try {
    // Fetch user details
    $stmt = $pdo->prepare("SELECT cust_name, cust_phone, cust_address, cust_city, cust_zip FROM customer WHERE cust_email = :cust_email");
    $stmt->execute([':cust_email' => $cust_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User details not found']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            echo json_encode(['status' => 'error', 'message' => 'Cart is empty or invalid']);
            exit;
        }

        // Gather POST data
        $payment_method = $_POST['payment_method'] ?? '';
        $reference_number = ($payment_method === 'cop') ? '0' : ($_POST['reference_number'] ?? null);
        $amount_paid = ($payment_method === 'cop') 
            ? array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $_SESSION['cart'])) 
            : ($_POST['amount_paid'] ?? null);

        if (empty($payment_method)) {
            echo json_encode(['status' => 'error', 'message' => 'Payment method is required']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total, full_name, address, city, postal_code, phone, created_at) 
                VALUES (:customer_id, :total, :full_name, :address, :city, :postal_code, :phone, NOW())");
            $stmt->execute([
                ':customer_id' => $cust_id,
                ':total' => array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $_SESSION['cart'])),
                ':full_name' => $user['cust_name'],
                ':address' => $user['cust_address'],
                ':city' => $user['cust_city'],
                ':postal_code' => $user['cust_zip'],
                ':phone' => $user['cust_phone']
            ]);

            $order_id = $pdo->lastInsertId();

            // Insert order items
            foreach ($_SESSION['cart'] as $product_id => $item) {
                // Validate product ID and stock
                $stmt = $pdo->prepare("SELECT p_id, quantity FROM product WHERE p_id = :product_id");
                $stmt->execute([':product_id' => $product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    echo json_encode(['status' => 'error', 'message' => "Product with ID $product_id does not exist."]);
                    $pdo->rollBack();
                    exit;
                }

                if ($product['quantity'] < $item['quantity']) {
                    echo json_encode(['status' => 'error', 'message' => "Insufficient stock for product ID $product_id."]);
                    $pdo->rollBack();
                    exit;
                }

                // Insert into order_items
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (:order_id, :product_id, :quantity, :price)");
                $stmt->execute([
                    ':order_id' => $order_id,
                    ':product_id' => $product_id,
                    ':quantity' => $item['quantity'],
                    ':price' => $item['price']
                ]);

                // Deduct product stock
                $stmt = $pdo->prepare("UPDATE product SET quantity = quantity - :quantity WHERE p_id = :product_id");
                $stmt->execute([':quantity' => $item['quantity'], ':product_id' => $product_id]);
            }

            // Insert payment
            $stmt = $pdo->prepare("INSERT INTO payment (cust_id, order_id, cust_name, cust_email, reference_number, amount_paid, payment_method, payment_status, shipping_status, created_at) 
                VALUES (:cust_id, :order_id, :cust_name, :cust_email, :reference_number, :amount_paid, :payment_method, 'pending', 'pending', NOW())");
            $stmt->execute([
                ':cust_id' => $cust_id,
                ':order_id' => $order_id,
                ':cust_name' => $user['cust_name'],
                ':cust_email' => $cust_email,
                ':reference_number' => $reference_number,
                ':amount_paid' => $amount_paid,
                ':payment_method' => $payment_method
            ]);

            $pdo->commit();
            unset($_SESSION['cart']);
            echo json_encode(['status' => 'success', 'message' => 'Order placed successfully']);
        } catch (Exception $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
