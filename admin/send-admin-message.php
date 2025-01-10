<?php
require_once('header.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$cust_id = $_POST['cust_id'];
$message = $_POST['message'];

$query = $pdo->prepare("
    INSERT INTO messages (customer_id, sender_type, message_text, created_at) 
    VALUES (:cust_id, 'admin', :message, NOW())
");
$query->execute([
    'cust_id' => $cust_id,
    'message' => $message
]);

echo json_encode(['status' => 'Message sent']);
?>
