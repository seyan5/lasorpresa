<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];
    $shipping_status = $_POST['shipping_status'];

    try {
        $stmt = $pdo->prepare("
            UPDATE payment 
            SET payment_status = :payment_status, shipping_status = :shipping_status 
            WHERE order_id = :order_id
        ");
        $stmt->execute([
            ':payment_status' => $payment_status,
            ':shipping_status' => $shipping_status,
            ':order_id' => $order_id,
        ]);

        echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
