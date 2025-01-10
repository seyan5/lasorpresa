<?php
ob_start();
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");

require '../mail/PHPMailer/src/Exception.php';
require '../mail/PHPMailer/src/PHPMailer.php';
require '../mail/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['register'])) {
    $cust_name = $_POST['cust_name'];
    $cust_email = $_POST['cust_email'];
    $cust_phone = $_POST['cust_phone'];
    $cust_password = password_hash($_POST['cust_password'], PASSWORD_BCRYPT);  // Hash password
    $cust_address = $_POST['cust_address'];
    $cust_city = $_POST['cust_city'];
    $cust_zip = $_POST['cust_zip'];
    $cust_status = 'inactive';  // Set the customer status as inactive until verified

    try {
        // Check if the email already exists
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM customer WHERE cust_email = :cust_email");
        $stmt_check->execute([':cust_email' => $cust_email]);
        $email_exists = $stmt_check->fetchColumn();

        if ($email_exists > 0) {
            throw new Exception("Email address already registered. Please use a different email.");
        }

        // Generate a unique verification token
        $token = bin2hex(random_bytes(16));

        // Insert customer details into the database
        $stmt = $pdo->prepare("INSERT INTO customer (cust_name, cust_email, cust_phone, cust_password, cust_address, cust_city, cust_zip, cust_status, cust_datetime) 
                               VALUES (:cust_name, :cust_email, :cust_phone, :cust_password, :cust_address, :cust_city, :cust_zip, :cust_status, NOW())");

        $stmt->execute([
            ':cust_name' => $cust_name,
            ':cust_email' => $cust_email,
            ':cust_phone' => $cust_phone,
            ':cust_password' => $cust_password,
            ':cust_address' => $cust_address,
            ':cust_city' => $cust_city,
            ':cust_zip' => $cust_zip,
            ':cust_status' => $cust_status
        ]);

        // Get the customer ID
        $cust_id = $pdo->lastInsertId();

        // Insert token into the email_verifications table
        $stmt_token = $pdo->prepare("INSERT INTO email_verifications (cust_id, token, created_at) VALUES (:cust_id, :token, NOW())");
        $stmt_token->execute([
            ':cust_id' => $cust_id,
            ':token' => $token
        ]);

        // Send verification email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'jpdeogracias@gmail.com'; // Your email
        $mail->Password   = 'scut aysl nlei jyng'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('lasorpresa@gmail.com', 'Lasorpresa'); // From address
        $mail->addAddress($cust_email, $cust_name);          // Recipient

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address';
        $mail->Body    = "Hi $cust_name,<br><br>Please click the link below to verify your email address:<br><br><a href='http://localhost/lasorpresa/users/verify-email.php?token=$token'>Verify Email</a><br><br>Thank you!";

        $mail->send();
        echo "Registration successful! Please check your email to verify your account.";
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
  <title>Customer Registration</title>
</head>
<body>
    <div class="logo-container">
        <img src="../images/logo.png" alt="Logo" class="logo" />
    </div>

    <!-- Flower Image -->
    <div class="flower-container">
        <img src="../images/flower2.png" alt="Flower" class="flower" />
    </div>

<h2>Create Your Account</h2>
<form action="register.php" method="POST">
    <div class="infield">
        <label for="cust_name">Full Name:</label>
        <input placeholder="Fullname" type="text" id="cust_name" name="cust_name" required><br>
    </div>
    <div class="infield">
        <label for="cust_email">Email:</label>
        <input placeholder="Email" type="email" id="cust_email" name="cust_email" required><br>
    </div>
    <div class="infield">
        <label for="cust_phone">Phone Number:</label>
        <input placeholder="Phone Number" type="tel" id="cust_phone" name="cust_phone" required><br>
    </div>
    <div class="infield">
        <label for="cust_password">Password:</label>
        <input placeholder="Password" type="password" id="cust_password" name="cust_password" required><br>
    </div>
    <div class="infield">
        <label for="cust_address">Address:</label>
        <input placeholder="Address" type="text" id="cust_address" name="cust_address" required><br>
    </div>
    <div class="infield">
        <label for="cust_city">City:</label>
        <input placeholder="City" type="text" id="cust_city" name="cust_city" required><br>
    </div>
    <div class="infield">
        <label for="cust_zip">Zip Code:</label>
        <input placeholder="Zip Code" type="text" id="cust_zip" name="cust_zip" required><br>
    </div>
    <p style="text-align: center; margin-top: 10px; font-size: 14px;">
        Already have an account? 
        <a href="login.php" style="color: #e18aaa; font-weight: bold; text-decoration: none;">Sign In</a>
    </p>
    <button type="submit" name="register">Register</button>
</form>

</body>
</html>