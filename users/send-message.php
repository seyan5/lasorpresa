<?php
require_once('header.php');

$cust_id = $_SESSION['customer']['cust_id'];
$message = trim($_POST['message'] ?? '');

if (!empty($message)) {
    $stmt = $pdo->prepare("INSERT INTO chat_messages (sender_id, sender_type, message) VALUES (:cust_id, 'customer', :message)");
    $stmt->execute(['cust_id' => $cust_id, 'message' => $message]);
}
?>
