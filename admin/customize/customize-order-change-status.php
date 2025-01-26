<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

// Include PHPMailer files
require '../../mail/PHPMailer/src/Exception.php';
require '../../mail/PHPMailer/src/PHPMailer.php';
require '../../mail/PHPMailer/src/SMTP.php';

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
        $mail->setFrom('lasorpresa@gmail.com', 'Lasorpreas'); // Replace with your sender email and name
        $mail->addAddress($customerEmail, $customerName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Customize Order Status Update: Order ID #' . $orderItemId;
        $mail->Body = "
            <h3>Dear $customerName,</h3>
            <p>The status of your customized order (Order ID: <strong>$orderItemId</strong>) has been updated:</p>
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
