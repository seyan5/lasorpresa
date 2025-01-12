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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $payment_status = $_POST['payment_status'];
    $shipping_status = $_POST['shipping_status'];

    try {
        // Update the payment and shipping status
        $stmt = $pdo->prepare("
            UPDATE payment 
            SET payment_status = :payment_status, shipping_status = :shipping_status 
            WHERE order_id = :order_id
        ");
        $stmt->execute([
            ':payment_status' => $payment_status,
            ':shipping_status' => $shipping_status,
            ':order_id' => $order_id,
        ]);

        // Fetch customer information for email notification
        $stmt = $pdo->prepare("
            SELECT c.cust_email, c.cust_name 
            FROM customer c
            JOIN orders o ON c.cust_id = o.customer_id
            WHERE o.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $order_id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($shipping_status === 'shipped' && $customer) {
            sendShippingNotification($customer['cust_email'], $customer['cust_name'], $order_id);
        }

        echo json_encode(['success' => true, 'message' => 'Order status updated successfully.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function sendShippingNotification($customerEmail, $customerName, $orderId) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
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

        $mail->send();
    } catch (Exception $e) {
        error_log("Error sending email: {$mail->ErrorInfo}");
    }
}
