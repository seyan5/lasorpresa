<?php
require_once('header.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$cust_id = $_POST['cust_id']; // Customer ID for admin to view specific chats
$query = $pdo->prepare("
    SELECT sender_type, message_text, created_at 
    FROM messages 
    WHERE customer_id = :cust_id 
    ORDER BY created_at ASC
");
$query->execute(['cust_id' => $cust_id]);
$messages = $query->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
?>
