<?php 
require_once('conn.php');

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
            // End PHP processing and output HTML/JavaScript
            echo "<!DOCTYPE html>
            <html lang='en'>
            <head>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        title: 'Email Already Registered!',
                        text: 'Please use a different email address.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'register.php'; // Redirect to the registration page
                    });
                </script>
            </body>
            </html>";
            exit; // Stop further script execution
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
        $mail->Username   = 'shoplasorpresa@gmail.com'; // Your email
        $mail->Password   = 'ooki ypuc vfqr qlqt'; // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('shoplasorpresa@gmail.com', 'Lasorpresa'); // From address
        $mail->addAddress($cust_email, $cust_name);          // Recipient

        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email Address';
        $mail->Body    = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    background-color: #f4f4f4;
                }
                .container {
                    width: 100%;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #ffffff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    color: #333;
                    font-size: 24px;
                    text-align: center;
                }
                p {
                    color: #555;
                    font-size: 16px;
                    line-height: 1.6;
                    text-align: center;
                }
                a {
                    display: block;
                    width: 200px;
                    padding: 10px 20px;
                    margin: 20px auto;
                    background-color: #e18aaa;
                    color: #ffffff;
                    text-decoration: none;
                    text-align: center;
                    border-radius: 5px;
                    font-weight: bold;
                }
                a:hover {
                    background-color: #c9778f;
                }
                .footer {
                    text-align: center;
                    font-size: 14px;
                    color: #777;
                    margin-top: 20px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='logo'>
                    <!-- Update logo source to reflect your hosted domain -->
                    <img src='https://lasorpresa.shop/images/logo.png' alt='Logo'>
                </div>
                <h1>Email Verification</h1>
                <p>Hi $cust_name,</p>
                <p>Please click the link below to verify your email address:</p>
                <!-- Update the verification URL to reflect the new domain -->
                <a href='https://lasorpresa.shop/users/verify-email.php?token=$token'>Verify Email</a>
                <p>Thank you!</p>
                <div class='footer'>
                    <p>If you did not request this, please ignore this email.</p>
                </div>
            </div>
        </body>

        </html>";

        try {
            $mail->send();
            echo "Email sent successfully.<br>"; // Debug statement
        } catch (Exception $e) {
            echo "Mailer Error: " . $mail->ErrorInfo . "<br>";
            exit; // Exit if there's an issue with email sending
        }

        echo "
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            Swal.fire({
                title: 'Registration Successful!',
                text: 'Please check your email to verify your account.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'login.php'; // Redirect to the login page after the alert
            });
        </script>";

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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="logo-container">
        <img src="../images/logo.png" alt="Logo" class="logo" />
    </div>

    <div class="flower-container">
        <img src="../images/flower2.png" alt="Flower" class="flower" />
    </div>

<h2>Create Your Account</h2>
<form action="register.php" method="POST" id="registerForm">
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
        <small id="password-strength-status"></small><br>
        <div id="password-strength-meter"></div>
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

    <div class="tos" style="font-size: 12px; display: flex; align-items: center; white-space: nowrap;">
        <input type="checkbox" id="terms" name="terms" required style="margin-right: 5px;">
        <label for="terms" style="margin-bottom: 0; font-size: 12px;">I agree to the 
            <a href="../tos.php" id="termsLink" style="color: #e18aaa; font-weight: bold;">Terms and Conditions</a>
        </label>
    </div>

    <p style="text-align: center; margin-top: 10px; font-size: 14px;">
        Already have an account? 
        <a href="login.php" style="color: #e18aaa; font-weight: bold; text-decoration: none;">Sign In</a>
    </p>
    <button type="submit" name="register">Register</button>
</form>

<script>
    // Real-time password strength indicator
    const passwordField = document.getElementById('cust_password');
    const strengthMeter = document.getElementById('password-strength-meter');
    const strengthStatus = document.getElementById('password-strength-status');

    passwordField.addEventListener('input', function() {
        const password = passwordField.value;
        let strength = 0;

        // Check password strength
        if (password.length >= 8) strength++; // Length check
        if (/[A-Z]/.test(password)) strength++; // Uppercase letter check
        if (/[a-z]/.test(password)) strength++; // Lowercase letter check
        if (/[0-9]/.test(password)) strength++; // Number check
        if (/[\W_]/.test(password)) strength++; // Special character check

        // Update strength meter
        if (strength === 0) {
            strengthMeter.style.width = '0';
            strengthStatus.textContent = '';
        } else if (strength === 1) {
            strengthMeter.style.width = '25%';
            strengthStatus.textContent = 'Weak';
            strengthStatus.style.color = 'red';
        } else if (strength === 2) {
            strengthMeter.style.width = '50%';
            strengthStatus.textContent = 'Fair';
            strengthStatus.style.color = 'orange';
        } else if (strength === 3) {
            strengthMeter.style.width = '75%';
            strengthStatus.textContent = 'Good';
            strengthStatus.style.color = 'yellowgreen';
        } else {
            strengthMeter.style.width = '100%';
            strengthStatus.textContent = 'Strong';
            strengthStatus.style.color = 'green';
        }
    });
</script>

</body>
</html>
