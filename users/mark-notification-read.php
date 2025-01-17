<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'])) {
        $orderId = $_POST['order_id'];

        // Update the notification status to "read"
        $stmt = $pdo->prepare("UPDATE payment SET notification_read = 1 WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);

        echo json_encode(['success' => true]);
    }
}
