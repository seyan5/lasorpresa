<?php
require 'header.php'; // Ensure this file includes the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Your cart is empty!'
        ]);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Create a new order
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_price, order_date) VALUES (:customer_id, :total_price, NOW())");
        $stmt->execute([
            ':customer_id' => $_SESSION['customer']['cust_id'],
            ':total_price' => array_sum(array_map(function($item) {
                return $item['price'] * $item['quantity'];
            }, $_SESSION['cart']))
        ]);

        $order_id = $pdo->lastInsertId();

        // Add items to the order and update product quantities
        foreach ($_SESSION['cart'] as $product_id => $item) {
            // Insert order items
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)
            ");
            $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);

            // Update product quantity in the database
            $stmt = $pdo->prepare("
                UPDATE product
                SET p_qty = p_qty - :quantity
                WHERE p_id = :product_id AND p_qty >= :quantity
            ");
            $stmt->execute([
                ':quantity' => $item['quantity'],
                ':product_id' => $product_id
            ]);

            // Check if the update affected any rows (to ensure sufficient stock)
            if ($stmt->rowCount() === 0) {
                throw new Exception("Insufficient stock for product ID: $product_id");
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Clear the cart
        unset($_SESSION['cart']);

        echo json_encode([
            'success' => true,
            'message' => 'Order placed successfully!',
            'order_id' => $order_id
        ]);
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method!'
    ]);
}
?>
