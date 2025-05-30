<?php
ob_start();
session_start();
include_once('../conn.php');

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

    // Check if the new_status is valid for shipping_status
    $validShippingStatuses = ['shipped', 'delivered', 'readyforpickup']; // Add 'readyforpickup' here
    if ($status_column === 'shipping_status' && !in_array($new_status, $validShippingStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid shipping status']);
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
            if ($status_column === 'shipping_status' && in_array($new_status, ['shipped', 'delivered', 'readyforpickup'])) {
                // Fetch the customer details
                $stmt = $pdo->prepare("
    SELECT 
        c.cust_email, 
        c.cust_name, 
        p.payment_method, 
        oi.product_id, 
        pr.name AS product_name, 
        oi.quantity, 
        oi.price,
        pr.featured_photo
    FROM customer c
    JOIN orders o ON c.cust_id = o.customer_id
    JOIN payment p ON o.order_id = p.order_id
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN product pr ON oi.product_id = pr.p_id
    WHERE o.order_id = :order_id
");
$stmt->execute([':order_id' => $order_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if ($customer) {
    sendShippingNotification(
        $customer['cust_email'], 
        $customer['cust_name'], 
        $order_id, 
        $new_status, 
        $customer['payment_method'], 
        $customer['product_name'], 
        $customer['quantity'], 
        $customer['price'],
        $customer['featured_photo']
    );
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



function embedImage($filePath) {
    // Check if the file exists
    if (!file_exists($filePath)) {
        return null;
    }

    // Read the file content
    $fileContent = file_get_contents($filePath);
    
    // Get the file type (image)
    $fileType = mime_content_type($filePath);

    // Encode the file content in Base64
    $base64Image = base64_encode($fileContent);
    
    // Create a data URL for the image
    return 'data:' . $fileType . ';base64,' . $base64Image;
}

function sendShippingNotification($customerEmail, $customerName, $orderId, $shippingStatus, $paymentMethod, $productName, $quantity, $price, $featuredPhoto) {
    $mail = new PHPMailer(true);

    try {
        // Embed the image if it's available
        $encodedImage = embedImage($featuredPhoto);

        // Email content based on shipping status
        $subject = 'Order ' . ucfirst($shippingStatus) . ': Order ID #' . $orderId;
        $body = "<h3>Dear $customerName,</h3>
                 <p>Your order has been $shippingStatus.</p>
                 <p><strong>Payment Method:</strong> $paymentMethod</p>
                 <p><strong>Product:</strong> $productName</p>
                 <p><strong>Quantity:</strong> $quantity</p>
                 <p><strong>Price:</strong> ₱$price</p>";

        // If image is available, include it
        if ($encodedImage) {
            $body .= "<p><strong>Product Image:</strong><br><img src='$encodedImage' alt='$productName' style='max-width: 200px;'></p>";
        }

        $body .= "<p>Thank you for shopping with us!</p>
                  <p>Best Regards,<br>Your Flower Shop</p>";



                  

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'shoplasorpresa@gmail.com'; // Replace with your email
        $mail->Password = 'ooki ypuc vfqr qlqt'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('shoplasorpresa@gmail.com', 'Lasorpresa');
        $mail->addAddress($customerEmail, $customerName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        error_log("Error sending email: {$mail->ErrorInfo}");
    }
}
?>
