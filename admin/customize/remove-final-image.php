<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $orderId = $data['order_id'] ?? null;
    $imageName = $data['image_name'] ?? null;

    if ($orderId && $imageName) {
        // Check if the image exists in the database
        $stmt = $pdo->prepare("SELECT final_image FROM custom_finalimages WHERE order_id = :order_id AND final_image = :final_image");
        $stmt->execute([':order_id' => $orderId, ':final_image' => $imageName]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $filePath = 'final_image_uploads/' . $image['final_image'];

            // Remove the image file from the server
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Remove the image from the database
            $stmt = $pdo->prepare("DELETE FROM custom_finalimages WHERE order_id = :order_id AND final_image = :final_image");
            $stmt->execute([':order_id' => $orderId, ':final_image' => $imageName]);

            echo json_encode(['success' => true, 'message' => 'Final image removed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Image not found in the database.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
