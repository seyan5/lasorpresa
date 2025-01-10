<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Chat Dashboard</title>
    <script>
        setInterval(function () {
            fetchAdminMessages();
        }, 2000);

        function fetchAdminMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch-admin-message.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("admin-chat-box").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function sendAdminMessage() {
            const message = document.getElementById("admin-message-input").value;
            const customerId = document.getElementById("customer-id").value;
            if (message.trim() === "") return;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send-admin-message.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("admin-message-input").value = "";
                    fetchAdminMessages();
                }
            };
            xhr.send("message=" + encodeURIComponent(message) + "&customer_id=" + customerId);
        }
    </script>
</head>
<body>
    <h2>Admin Chat</h2>
    
    <select id="customer-id">
    <?php
    $stmt = $pdo->query("SELECT cust_id, cust_name FROM customer");
    while ($customer = $stmt->fetch()) {
        echo "<option value='{$customer['cust_id']}'>{$customer['cust_name']}</option>";
    }
    ?>
</select>

    <div id="admin-chat-box" class="chat-box"></div>
    
    <textarea id="admin-message-input" placeholder="Type your message here..."></textarea>
    <button onclick="sendAdminMessage()">Send Message</button>
</body>
</html>
