<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Function to check if the user is an admin.
 */
function ensureAdmin() {
    // Check if the user is logged in and has the admin role
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        // Display a message or add HTML content before redirecting
        echo '
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Redirecting to Login</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f9;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    text-align: center;
                }
                .message-box {
                    background-color: #fff;
                    border-radius: 8px;
                    padding: 20px;
                    text-align: center;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    max-width: 400px;
                    width: 100%;
                }
                .message-box h1 {
                    color: #f44336;
                    font-size: 24px;
                    margin-bottom: 10px;
                }
                .message-box p {
                    color: #555;
                    font-size: 16px;
                    margin-bottom: 20px;
                }
                .message-box a {
                    text-decoration: none;
                    color: #2196f3;
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="message-box">
                <h1>Access Denied</h1>
                <p>You are not authorized to view this page.</p>
                <p>Redirecting you to the login page in 3 seconds...</p>
                <p>If you are not redirected, click <a href="/lasorpresa/admin/login.php">here</a>.</p>
            </div>
        </body>
        </html>
        ';
        // Delay the redirect for 3 seconds
        header("refresh:3;url=/lasorpresa/admin/login.php");
        exit;
    }
}

// Call the function at the top of your admin-restricted pages
ensureAdmin();
?>