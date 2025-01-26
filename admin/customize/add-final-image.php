<?php
ob_start();
session_start();
include_once('../conn.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['orderitem_id'], $_POST['order_id']) && isset($_FILES['final_image'])) {
        $orderitemId = $_POST['orderitem_id'];
        $orderId = $_POST['order_id'];
        $finalImage = $_FILES['final_image'];

        // Validate the uploaded file
        $targetDir = "final_image_uploads/";
        $targetFile = $targetDir . basename($finalImage['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Allowed file types
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imageFileType, $allowedTypes)) {
            echo json_encode([
                'success' => false,
                'message' => 'Only JPG, JPEG, PNG, and GIF files are allowed.',
            ]);
            exit;
        }

        // Move the uploaded file
        if (move_uploaded_file($finalImage['tmp_name'], $targetFile)) {
            try {
                // Insert into custom_finalimages table
                $stmt = $pdo->prepare("
                    INSERT INTO custom_finalimages (orderitem_id, order_id, final_image)
                    VALUES (:orderitem_id, :order_id, :final_image)
                ");
                $stmt->execute([
                    ':orderitem_id' => $orderitemId,
                    ':order_id' => $orderId,
                    ':final_image' => basename($finalImage['name']),
                ]);

                echo json_encode([
                    'success' => true,
                    'message' => 'Final image added successfully!',
                ]);
            } catch (Exception $e) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to add final image: ' . $e->getMessage(),
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to upload image.',
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid input. Order item ID, order ID, and final image are required.',
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
    ]);
}
?>
