<?php
ob_start();
session_start();
include_once('../conn.php');



// Include PHPMailer files
require '../../mail/PHPMailer/src/Exception.php';
require '../../mail/PHPMailer/src/PHPMailer.php';
require '../../mail/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderItemId = isset($_POST['orderitem_id']) ? intval($_POST['orderitem_id']) : null;
    $field = isset($_POST['field']) ? $_POST['field'] : null;
    $value = isset($_POST['value']) ? trim($_POST['value']) : null;

    if (!$orderItemId || !$field || !$value || !in_array($field, ['payment_status', 'shipping_status'])) {
        echo json_encode(["success" => false, "message" => "Invalid request data."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE custom_payment SET $field = :value WHERE orderitem_id = :orderitem_id");
        $stmt->execute(['value' => $value, 'orderitem_id' => $orderItemId]);

        if ($stmt->rowCount() > 0) {
            // Fetch customer details to send the email
            $stmtCustomer = $pdo->prepare("
                SELECT co.customer_email, co.customer_name 
                FROM custom_orderitems coi
                JOIN custom_order co ON coi.order_id = co.order_id
                WHERE coi.orderitem_id = :orderitem_id
            ");
            $stmtCustomer->execute([':orderitem_id' => $orderItemId]);
            $customer = $stmtCustomer->fetch(PDO::FETCH_ASSOC);

            if ($customer) {
                $customerEmail = $customer['customer_email'];
                $customerName = $customer['customer_name'];

                // Call the function to send the email
                sendStatusChangeNotification($customerEmail, $customerName, $orderItemId, $field, $value);
            }

            echo json_encode(["success" => true, "message" => ucfirst($field) . " updated successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "No changes were made."]);
        }
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Failed to update order status: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

function sendStatusChangeNotification($customerEmail, $customerName, $orderItemId, $field, $value)
{
    $mail = new PHPMailer(true);

    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'jpdeogracias@gmail.com'; // Replace with your email
        $mail->Password = 'scut aysl nlei jyng'; // Replace with your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email recipients
        $mail->setFrom('lasorpresa@gmail.com', 'Lasorpresa'); // Replace with your sender email and name
        $mail->addAddress($customerEmail, $customerName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Customize Order Status Update: Order Item #' . $orderItemId;
        $mail->Body = "
            <h3>Dear $customerName,</h3>
            <p>The status of your customized order (Order Item ID: <strong>$orderItemId</strong>) has been updated:</p>
            <p><strong>" . ucfirst($field) . ":</strong> $value</p>
            <p>Thank you for shopping with us!</p>
            <p>Best regards,<br>Your Shop Name</p>
        ";

        // Send email
        $mail->send();
    } catch (Exception $e) {
        // Log the error for debugging
        error_log("Failed to send email: " . $mail->ErrorInfo);
    }
}
