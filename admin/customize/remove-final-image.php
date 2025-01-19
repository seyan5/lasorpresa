<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;

    if ($orderId) {
        // Check if the image exists in the database
        $stmt = $pdo->prepare("SELECT final_image FROM custom_finalimages WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $orderId]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $filePath = 'final_image_uploads/' . $image['final_image'];

            // Remove the image file from the server
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Remove the image from the database
            $stmt = $pdo->prepare("DELETE FROM custom_finalimages WHERE order_id = :order_id");
            $stmt->execute([':order_id' => $orderId]);

            echo json_encode(['success' => true, 'message' => 'Final image removed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Image not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
