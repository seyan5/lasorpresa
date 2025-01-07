<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

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

    // Prepare the query to insert the customer into the database
    $stmt = $pdo->prepare("INSERT INTO customer (cust_name, cust_email, cust_phone, cust_password, cust_address, cust_city, cust_zip, cust_status, cust_datetime) 
                           VALUES (:cust_name, :cust_email, :cust_phone, :cust_password, :cust_address, :cust_city, :cust_zip, :cust_status, NOW())");

    $stmt->bindParam(':cust_name', $cust_name);
    $stmt->bindParam(':cust_email', $cust_email);
    $stmt->bindParam(':cust_phone', $cust_phone);
    $stmt->bindParam(':cust_password', $cust_password);
    $stmt->bindParam(':cust_address', $cust_address);
    $stmt->bindParam(':cust_city', $cust_city);
    $stmt->bindParam(':cust_zip', $cust_zip);
    $stmt->bindParam(':cust_status', $cust_status);

    if ($stmt->execute()) {
        // Get the customer ID of the newly created customer
        $cust_id = $pdo->lastInsertId();

        // Insert the token into a verification table
        $stmt_token = $pdo->prepare("INSERT INTO email_verifications (cust_id, token, created_at) VALUES (:cust_id, :token, NOW())");
        $stmt_token->bindParam(':cust_id', $cust_id);
        $stmt_token->bindParam(':token', $token);
        $stmt_token->execute();

        // Send verification email
        $verification_link = "http://yourdomain.com/verify-email.php?token=$token";

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';  // Set the SMTP server
            $mail->SMTPAuth   = true;
            $mail->Username   = 'lasorpresa76@gmail.com';  // Your email address
            $mail->Password   = 'lasorpresa123!';  // Your email password or app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('Lasorpresa@gmail.com', 'Lasorpresa');
            $mail->addAddress($cust_email, $cust_name);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Email Verification';
            $mail->Body    = "Hi $cust_name,<br><br>Please click the link below to verify your email address:<br><br>
                              <a href='$verification_link'>$verification_link</a><br><br>Thank you!";
            $mail->AltBody = "Hi $cust_name,\n\nPlease click the link below to verify your email address:\n\n$verification_link\n\nThank you!";

            $mail->send();
            echo "Registration successful! Please check your email to verify your account.";
        } catch (Exception $e) {
            echo "Error sending verification email: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error registering customer. Please try again.";
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
