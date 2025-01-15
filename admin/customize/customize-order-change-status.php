<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;
    $paymentStatus = isset($_POST['payment_status']) ? $_POST['payment_status'] : null;
    $shippingStatus = isset($_POST['shipping_status']) ? $_POST['shipping_status'] : null;

    if (!$orderId || (!$paymentStatus && !$shippingStatus)) {
        echo json_encode(["success" => false, "message" => "Invalid request data."]);
        exit;
    }

    try {
        if ($paymentStatus) {
            $stmt = $pdo->prepare("UPDATE custom_payment SET payment_status = :payment_status WHERE order_id = :order_id");
            $stmt->execute(['payment_status' => $paymentStatus, 'order_id' => $orderId]);
        }

        if ($shippingStatus) {
            $stmt = $pdo->prepare("UPDATE custom_payment SET shipping_status = :shipping_status WHERE order_id = :order_id");
            $stmt->execute(['shipping_status' => $shippingStatus, 'order_id' => $orderId]);
        }

        echo json_encode(["success" => true, "message" => "Order status updated successfully."]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to update order status: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>