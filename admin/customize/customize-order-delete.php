<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;

    if (!$orderId) {
        echo json_encode(["success" => false, "message" => "Invalid request data."]);
        exit;
    }

    try {
        // Delete from custom_orderitems
        $stmt = $pdo->prepare("DELETE FROM custom_orderitems WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        // Delete from custom_payment
        $stmt = $pdo->prepare("DELETE FROM custom_payment WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        // Delete from custom_order
        $stmt = $pdo->prepare("DELETE FROM custom_order WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        echo json_encode(["success" => true, "message" => "Order deleted successfully."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to delete order: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>