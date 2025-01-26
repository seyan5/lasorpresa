<?php
ob_start();
session_start();
include_once('../conn.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    try {
        $order_id = $_POST['order_id'];

        // Check if the order exists
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);
        $order = $stmt->fetch();

        if (!$order) {
            throw new Exception("Order not found");
        }

        // Start the transaction
        $pdo->beginTransaction();

        // Delete associated payment data
        $stmt = $pdo->prepare("DELETE FROM payment WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);

        // Delete associated order items
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);

        // Delete the order
        $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);

        // Commit the transaction
        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
