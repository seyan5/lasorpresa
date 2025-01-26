<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = $_POST['otp'];

    if (isset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['user_data'])) {
        if ($entered_otp == $_SESSION['otp']) {
            // OTP is correct, log the user in
            $user = $_SESSION['user_data'];

            // Set session variables for logged-in user
            $_SESSION['id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_type'] = $user['user_type'];

            // Clear OTP session variables
            unset($_SESSION['otp'], $_SESSION['otp_email'], $_SESSION['user_data']);

            // Redirect based on user type
            if ($user['user_type'] === 'admin') {
                showMessage(
                    "Login Successful!", 
                    "Welcome, Admin! Redirecting to your dashboard...", 
                    "success", 
                    "dashboard.php"
                );
                exit;
            } else {
                showMessage(
                    "Access Denied!", 
                    "You are not an admin. Redirecting to the users page...", 
                    "error", 
                    "../users/index.php"
                );
                exit;
            }
        } else {
            showMessage(
                "Invalid OTP!", 
                "Please try again. Redirecting to OTP verification...", 
                "error", 
                "verify-otp.php"
            );
            exit;
        }
    } else {
        showMessage(
            "Session Expired!", 
            "Invalid access. Redirecting to the login page...", 
            "error", 
            "login.php"
        );
        exit;
    }
}

function showMessage($title, $message, $status, $redirectUrl) {
    $statusColor = $status === "success" ? "#28a745" : "#dc3545"; // Green for success, Red for error
    echo "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>$title</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f8f9fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .message-container {
                text-align: center;
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                max-width: 400px;
                width: 100%;
            }
            .message-container h1 {
                color: $statusColor;
                margin-bottom: 10px;
            }
            .message-container p {
                color: #333;
                margin-bottom: 20px;
            }
            .message-container a {
                text-decoration: none;
                color: #007bff;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class='message-container'>
            <h1>$title</h1>
            <p>$message</p>
            <p><small>If you are not redirected, <a href='$redirectUrl'>click here</a>.</small></p>
        </div>
    </body>
    </html>
    ";
    header("refresh:2; url=$redirectUrl");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .container label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #555;
        }
        .container input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .container button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .container button:hover {
            background-color: #0056b3;
        }
        .container small {
            display: block;
            margin-top: 10px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify OTP</h2>
        <form action="verify-otp.php" method="POST">
            <label for="otp">Enter OTP:</label>
            <input type="text" id="otp" name="otp" placeholder="Enter your OTP" required>
            <button type="submit">Verify</button>
        </form>
        <small>Please check your email for the OTP.</small>
    </div>
</body>
</html>

