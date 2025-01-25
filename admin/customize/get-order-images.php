<?php
ob_start();
session_start();
include("../inc/config.php");

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Fetch expected images
    $stmt = $pdo->prepare("SELECT expected_image FROM custom_images WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $orderId]);
    $expectedImages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch final images
    $stmt = $pdo->prepare("SELECT final_image FROM custom_finalimages WHERE order_id = :order_id");
    $stmt->execute([':order_id' => $orderId]);
    $finalImages = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'expected_images' => $expectedImages,
        'final_images' => $finalImages,
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID.']);
}
?>
