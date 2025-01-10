<?php
require_once('header.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message']) && !empty($_POST['customer_id'])) {
    $customer_id = (int) $_POST['customer_id'];
    $message = trim($_POST['message']);

    $stmt = $pdo->prepare("INSERT INTO chat_messages (sender_id, sender_type, message) VALUES (:customer_id, 'admin', :message)");
    $stmt->execute(['customer_id' => $customer_id, 'message' => $message]);

    header('Location: admin_chat.php');
    exit;
}
?>
