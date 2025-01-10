<?php
session_start();
require_once('db_connection.php'); // Connect to DB

// Check if user is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: login.php');
    exit;
}

$cust_id = $_SESSION['customer']['cust_id'];

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    $stmt = $pdo->prepare("INSERT INTO chat_messages (sender_id, sender_type, message) VALUES (:sender_id, 'customer', :message)");
    $stmt->execute(['sender_id' => $cust_id, 'message' => $message]);
}

// Retrieve chat messages
$stmt = $pdo->prepare("SELECT * FROM chat_messages WHERE sender_id = :cust_id OR sender_type = 'admin' ORDER BY timestamp ASC");
$stmt->execute(['cust_id' => $cust_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Chat</title>
</head>
<body>
    <h2>Chat with Support</h2>

    <div class="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div>
                <strong><?php echo ($msg['sender_type'] == 'customer') ? 'You' : 'Admin'; ?>:</strong>
                <?php echo htmlspecialchars($msg['message']); ?>
                <small>(<?php echo $msg['timestamp']; ?>)</small>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST">
        <textarea name="message" required></textarea><br>
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
