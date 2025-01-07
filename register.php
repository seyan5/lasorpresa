<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");

// Include PHPMailer
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

    // Generate a unique verification token
    $token = bin2hex(random_bytes(16));

    try {
        // Insert customer details into database
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

        // Insert token into email_verifications table
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
        $mail->Username   = 'lasorpresa76@gmail.com'; // Your email
        $mail->Password   = 'lasorpresa123!'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('lasorpresa@gmail.com', 'Lasorpresa'); // From address
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
