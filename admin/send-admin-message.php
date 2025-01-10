<?php
require_once('header.php');

$message = trim($_POST['message'] ?? '');
$customer_id = intval($_POST['customer_id'] ?? 0);

if (!empty($message) && $customer_id > 0) {
    $stmt = $pdo->prepare("INSERT INTO chat_messages (sender_id, sender_type, message) VALUES (:customer_id, 'admin', :message)");
    $stmt->execute(['customer_id' => $customer_id, 'message' => $message]);
}
?>
