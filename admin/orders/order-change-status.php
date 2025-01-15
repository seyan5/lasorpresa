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

try {
    $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $status_column = $_POST['status_column'] ?? null;
    $new_status = $_POST['new_status'] ?? null;

    // Validate input
    if (!$order_id || !$status_column || !$new_status) {
        echo json_encode(['success' => false, 'message' => 'Missing required parameters.']);
        exit;
    }

    // Check if the status_column is valid
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
            // Notify customer for shipping updates
            if ($status_column === 'shipping_status' && $new_status === 'shipped') {
                $stmt = $pdo->prepare("SELECT c.cust_email, c.cust_name FROM customer c 
                    JOIN orders o ON c.cust_id = o.customer_id 
                    WHERE o.order_id = :order_id");
                $stmt->execute([':order_id' => $order_id]);
                $customer = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($customer) {
                    sendShippingNotification($customer['cust_email'], $customer['cust_name'], $order_id);
                }
            }

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
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'your_email@gmail.com'; // Replace with your email
        $mail->Password = 'your_password'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('lasorpresa@gmail.com', 'Lasorpresa');
        $mail->addAddress($customerEmail, $customerName);

        $mail->isHTML(true);
        $mail->Subject = 'Order Shipped: Order ID #' . $orderId;
        $mail->Body = "<h3>Dear $customerName,</h3>
                       <p>Your order (Order ID: <strong>$orderId</strong>) has been shipped.</p>
                       <p>Thank you for shopping with us!</p>
                       <p>Best Regards,<br>Your Flower Shop</p>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Error sending email: {$mail->ErrorInfo}");
    }
}
