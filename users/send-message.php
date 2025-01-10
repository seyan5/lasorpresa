<?php
require_once('header.php'); // Replace with your DB connection

if (!isset($_SESSION['cust_id']) || empty($_POST['message'])) {
    exit('Invalid request');
}

$cust_id = $_SESSION['cust_id'];
$message_text = trim($_POST['message']);

$stmt = $pdo->prepare("INSERT INTO customer_messages (cust_id, message_text, sent_by) VALUES (:cust_id, :message_text, 'customer')");
$stmt->execute(['cust_id' => $cust_id, 'message_text' => $message_text]);

echo "Message Sent";
?>
