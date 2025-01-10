<?php
require_once('header.php'); // Replace with your DB connection

$cust_id = $_SESSION['cust_id'];  // Assuming customer login session is set

$stmt = $pdo->prepare("SELECT message_text, sent_by, timestamp FROM customer_messages WHERE cust_id = :cust_id ORDER BY timestamp ASC");
$stmt->execute(['cust_id' => $cust_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($messages as $message) {
    $sender = $message['sent_by'] == 'customer' ? 'You' : 'Admin';
    echo "<p><strong>{$sender}:</strong> {$message['message_text']} <em>({$message['timestamp']})</em></p>";
}
?>
