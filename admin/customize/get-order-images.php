<?php
ob_start();
session_start();
include("../inc/config.php");

if (isset($_GET['orderitem_id'])) { // Use orderitem_id instead of order_id
    $orderitemId = $_GET['orderitem_id'];

    try {
        // Fetch expected images for the given orderitem_id
        $stmt = $pdo->prepare("SELECT expected_image FROM custom_images WHERE orderitem_id = :orderitem_id");
        $stmt->execute([':orderitem_id' => $orderitemId]);
        $expectedImages = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Fetch final images for the given orderitem_id
        $stmt = $pdo->prepare("SELECT final_image FROM custom_finalimages WHERE orderitem_id = :orderitem_id");
        $stmt->execute([':orderitem_id' => $orderitemId]);
        $finalImages = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode([
            'success' => true,
            'expected_images' => $expectedImages,
            'final_images' => $finalImages,
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch images: ' . $e->getMessage(),
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid orderitem_id.']);
}
?>
