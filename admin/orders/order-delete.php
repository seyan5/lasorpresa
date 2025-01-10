<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];

    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // Delete order items associated with the order
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);

        // Delete the payment associated with the order
        $stmt = $pdo->prepare("DELETE FROM payment WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);

        // Delete the order itself
        $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);

        // Commit the transaction
        $pdo->commit();

        echo json_encode(['success' => true, 'message' => 'Order deleted successfully.']);
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}