<?php
ob_start();
session_start();
include_once('../conn.php');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode the JSON payload
    $data = json_decode(file_get_contents("php://input"), true);
    $orderItemId = $data['orderitem_id'] ?? null; // Use orderitem_id
    $imageName = $data['image_name'] ?? null;

    // Validate input
    if (!$orderItemId || !$imageName) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
        exit;
    }

    try {
        // Check if the image exists in the database
        $stmt = $pdo->prepare("SELECT final_image FROM custom_finalimages WHERE orderitem_id = :orderitem_id AND final_image = :final_image");
        $stmt->execute([
            ':orderitem_id' => $orderItemId,
            ':final_image' => $imageName,
        ]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($image) {
            $filePath = 'final_image_uploads/' . $image['final_image'];

            // Remove the image file from the server if it exists
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    echo json_encode(['success' => false, 'message' => 'Failed to delete the image file from the server.']);
                    exit;
                }
            }

            // Remove the image record from the database
            $stmt = $pdo->prepare("DELETE FROM custom_finalimages WHERE orderitem_id = :orderitem_id AND final_image = :final_image");
            $stmt->execute([
                ':orderitem_id' => $orderItemId,
                ':final_image' => $imageName,
            ]);

            echo json_encode(['success' => true, 'message' => 'Final image removed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Image not found in the database.']);
        }
    } catch (Exception $e) {
        // Handle unexpected errors
        echo json_encode([
            'success' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ]);
    }
} else {
    // Invalid request method
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
