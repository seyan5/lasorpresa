<?php
require_once('header.php');

// Check if user is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: login.php');
    exit;
}

$cust_id = $_SESSION['customer']['cust_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Support Chat</title>
    <style>
        .chat-box {
            border: 1px solid #ddd;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            background-color: #f9f9f9;
        }
        .chat-message {
            margin: 5px 0;
        }
        .customer { color: blue; }
        .admin { color: green; }
    </style>
    <script>
        // Function to fetch new messages every 2 seconds
        setInterval(function () {
            fetchMessages();
        }, 2000);

        function fetchMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch-messages.php", true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("chat-box").innerHTML = xhr.responseText;
                    document.getElementById("chat-box").scrollTop = document.getElementById("chat-box").scrollHeight;
                }
            };
            xhr.send();
        }

        // Function to send a new message
        function sendMessage() {
            const message = document.getElementById("message-input").value;
            if (message.trim() === "") return;
            
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send-message.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById("message-input").value = "";
                    fetchMessages();
                }
            };
            xhr.send("message=" + encodeURIComponent(message));
        }
    </script>
</head>
<body>
    <h2>Customer Chat</h2>
    <div id="chat-box" class="chat-box"></div>
    
    <textarea id="message-input" placeholder="Type your message here..."></textarea>
    <button onclick="sendMessage()">Send</button>
</body>
</html>
