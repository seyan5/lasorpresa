<?php
include('conn.php');



require '../mail/PHPMailer/src/Exception.php';
require '../mail/PHPMailer/src/PHPMailer.php';
require '../mail/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Query to get user details
        $stmt = $pdo->prepare("SELECT id, name, email, password, user_type FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if the password matches
            if (password_verify($password, $user['password'])) {
                // Generate a 6-digit OTP
                $otp = rand(100000, 999999);

                // Store OTP in session temporarily
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_email'] = $email;
                $_SESSION['user_data'] = $user; // Store user data for login post-OTP

                // Send OTP via email
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
                    $mail->SMTPAuth = true;
                    $mail->Username = 'jpdeogracias@gmail.com'; // Replace with your email
                    $mail->Password = 'scut aysl nlei jyng'; // Replace with your email password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port = 465;

                    $mail->setFrom('lasorpresa@gmail.com', 'Lasorpresa'); // Replace with your email
                    $mail->addAddress($email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your Login OTP';
                    $mail->Body = "Hello, <br>Your OTP for login is: <strong>$otp</strong>. It will expire in 5 minutes.";

                    $mail->send();

                    // Redirect to OTP verification page
                    header("Location: verify-otp.php");
                    exit;
                } catch (Exception $e) {
                    echo "Error sending OTP: " . $mail->ErrorInfo;
                }
            } else {
                echo "Incorrect password. Please try again.";
            }
        } else {
            echo "No account found with that email address.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../registerlogin.css">
    <title>Admin Login</title>
</head>
<body>

    <div class="logo-container">
        <img src="../images/logo.png" alt="Logo" class="logo" />
    </div>

    <!-- Flower Image -->
    <div class="flower-container">
        <img src="../images/flower2.png" alt="Flower" class="flower" />
    </div>

    <h2>Admin Login</h2>
    <form action="login.php" method="POST">
        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <div class="infield">
            <label for="email">Email:</label>
            <input placeholder="Email" type="email" id="email" name="email" required><br>
        </div>
        <div class="infield">
            <label for="password">Password:</label>
            <input placeholder="Password" type="password" id="password" name="password" required><br>
        </div>
        <p style="text-align: center; margin-top: 10px; font-size: 14px;">
            Don't Have an Account? 
            <a href="register.php" style="color: #e18aaa; font-weight: bold; text-decoration: none;">Register</a>
        </p>
        <button type="submit" name="login">Login</button>
    </form>

</body>
</html>

