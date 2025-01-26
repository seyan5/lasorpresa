<?php
ob_start();
session_start();
include_once('../conn.php');



header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;

    if (!$orderId) {
        echo json_encode(["success" => false, "message" => "Invalid request data."]);
        exit;
    }

    try {
        // Start a transaction
        $pdo->beginTransaction();

        // Delete related data from other tables
        $stmt = $pdo->prepare("DELETE FROM custom_images WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        $stmt = $pdo->prepare("DELETE FROM custom_finalimages WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        $stmt = $pdo->prepare("DELETE FROM custom_orderitems WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        $stmt = $pdo->prepare("DELETE FROM custom_payment WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        $stmt = $pdo->prepare("DELETE FROM custom_order WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        // Commit the transaction
        $pdo->commit();

        echo json_encode(["success" => true, "message" => "Order deleted successfully."]);
    } catch (Exception $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Failed to delete order: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

?>