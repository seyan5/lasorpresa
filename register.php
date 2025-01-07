<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");

require 'mail/PHPMailer/src/Exception.php';
require 'mail/PHPMailer/src/PHPMailer.php';
require 'mail/PHPMailer/src/SMTP.php';

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
        $mail->Username   = 'your_email@gmail.com'; // Your email
        $mail->Password   = 'your_email_password'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your_email@gmail.com', 'Your Name'); // From address
        $mail->addAddress($cust_email, $cust_name);          // Recipient

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address';
        $mail->Body    = "Hi $cust_name,<br><br>Please click the link below to verify your email address:<br><br><a href='http://yourdomain.com/verify-email.php?token=$token'>Verify Email</a><br><br>Thank you!";

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
  <title>Customer Registration</title>
</head>
<body>

<h2>Create Your Account</h2>
<form action="register.php" method="POST">
  <label for="cust_name">Full Name:</label>
  <input type="text" id="cust_name" name="cust_name" required><br>

  <label for="cust_email">Email:</label>
  <input type="email" id="cust_email" name="cust_email" required><br>

  <label for="cust_phone">Phone Number:</label>
  <input type="tel" id="cust_phone" name="cust_phone" required><br>

  <label for="cust_password">Password:</label>
  <input type="password" id="cust_password" name="cust_password" required><br>

  <label for="cust_address">Address:</label>
  <input type="text" id="cust_address" name="cust_address" required><br>

  <label for="cust_city">City:</label>
  <input type="text" id="cust_city" name="cust_city" required><br>

  <label for="cust_zip">Zip Code:</label>
  <input type="text" id="cust_zip" name="cust_zip" required><br>

  <button type="submit" name="register">Register</button>
</form>

</body>
</html>
