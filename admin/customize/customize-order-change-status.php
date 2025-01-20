<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : null;
    $field = isset($_POST['field']) ? $_POST['field'] : null;
    $value = isset($_POST['value']) ? trim($_POST['value']) : null;

    if (!$orderId || !$field || !$value || !in_array($field, ['payment_status', 'shipping_status'])) {
        echo json_encode(["success" => false, "message" => "Invalid request data."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE custom_payment SET $field = :value WHERE order_id = :order_id");
        $stmt->execute(['value' => $value, 'order_id' => $orderId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["success" => true, "message" => ucfirst($field) . " updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "No changes were made."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to update order status: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
