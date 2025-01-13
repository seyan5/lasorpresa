<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

require '../../mail/PHPMailer/src/Exception.php';
require '../../mail/PHPMailer/src/PHPMailer.php';
require '../../mail/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Disable error reporting to prevent HTML errors in the response
error_reporting(0); 
ini_set('display_errors', 0);

// Test database connection
try {
    $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status_column = $_POST['status_column'];
    $new_status = $_POST['new_status'];

    // Validate inputs
    if (!in_array($status_column, ['payment_status', 'shipping_status'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid status column']);
        exit;
    }

    try {
        // Update the order status
        $stmt = $pdo->prepare("UPDATE payment SET $status_column = :new_status WHERE order_id = :order_id");
        $stmt->execute([
            ':new_status' => $new_status,
            ':order_id' => $order_id
        ]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => ucfirst($status_column) . ' updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made to the order status.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

function sendShippingNotification($customerEmail, $customerName, $orderId) {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'jpdeogracias@gmail.com'; // Replace with your email
        $mail->Password = 'scut aysl nlei jyng'; // Replace with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('lasorpresa@gmail.com', 'Lasorpresa');
        $mail->addAddress($customerEmail, $customerName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Order Shipped: Order ID #' . $orderId;
        $mail->Body = "<h3>Dear $customerName,</h3>
                       <p>We are pleased to inform you that your order (Order ID: <strong>$orderId</strong>) has been shipped.</p>
                       <p>Thank you for shopping with us!</p>
                       <p>Best Regards,<br>Your Flower Shop</p>";

        // Send the email
        $mail->send();
    } catch (Exception $e) {
        error_log("Error sending email: {$mail->ErrorInfo}"); // Log the error but don't output to user
    }
}
