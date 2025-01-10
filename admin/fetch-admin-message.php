<?php
require_once('header.php');

$customer_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;

if ($customer_id > 0) {
    $stmt = $pdo->prepare("
        SELECT * FROM chat_messages 
        WHERE sender_id = :customer_id OR sender_type = 'admin' 
        ORDER BY timestamp ASC
    ");
    $stmt->execute(['customer_id' => $customer_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($messages as $msg) {
        $sender = $msg['sender_type'] == 'customer' ? 'Customer' : 'Admin';
        echo "<div class='chat-message'><strong class='{$msg['sender_type']}'>$sender:</strong> " .
             htmlspecialchars($msg['message']) .
             " <small>(" . $msg['timestamp'] . ")</small></div>";
    }
} else {
    echo "No customer selected.";
}
?>
