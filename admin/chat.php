<?php
require_once('header.php');

// Retrieve all customer messages
$stmt = $pdo->prepare("SELECT * FROM chat_messages ORDER BY timestamp ASC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Chat Dashboard</title>
</head>
<body>
    <h2>Admin Chat Dashboard</h2>

    <div class="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div>
                <strong><?php echo ($msg['sender_type'] == 'customer') ? 'Customer ID ' . $msg['sender_id'] : 'Admin'; ?>:</strong>
                <?php echo htmlspecialchars($msg['message']); ?>
                <small>(<?php echo $msg['timestamp']; ?>)</small>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Admin can send responses -->
    <form method="POST" action="chat-send-message.php">
        <label for="customer_id">Select Customer ID:</label>
        <input type="number" name="customer_id" required>
        <textarea name="message" required></textarea><br>
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
