<?php
require_once('header.php');

$cust_id = $_SESSION['customer']['cust_id'];

// Fetch chat messages for this customer
$stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE sender_id = :cust_id OR sender_type = 'admin' ORDER BY timestamp ASC");
$stmt->execute(['cust_id' => $cust_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $msg) {
    $sender = $msg['sender_type'] == 'customer' ? 'You' : 'Admin';
    echo "<div class='chat-message'><strong class='{$msg['sender_type']}'>$sender:</strong> " . 
         htmlspecialchars($msg['message']) . 
         " <small>(" . $msg['timestamp'] . ")</small></div>";
}
?>
