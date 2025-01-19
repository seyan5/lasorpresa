<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['order_id'] ?? null;
    $finalImage = $_FILES['final_image'] ?? null;

    if ($orderId && $finalImage) {
        $uploadDir = 'final_image_uploads/';
        $fileName = time() . '_' . basename($finalImage['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($finalImage['tmp_name'], $filePath)) {
            $stmt = $pdo->prepare("INSERT INTO custom_finalimages (order_id, final_image) VALUES (:order_id, :final_image)");
            $stmt->execute([
                ':final_image' => $fileName,
                ':order_id' => $orderId
            ]);

            echo json_encode(['success' => true, 'message' => 'Final image added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload the image.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    }

}
