<?php
session_start();
header('Content-Type: application/json');

// Check if the cart exists and the item index is provided
if (isset($_SESSION['cart']) && isset($_POST['item_index'])) {
    $itemIndex = (int) $_POST['item_index'];

    // Validate the index and remove the item
    if (isset($_SESSION['cart'][$itemIndex])) {
        array_splice($_SESSION['cart'], $itemIndex, 1);
        echo json_encode(['status' => 'success']);
        exit;
    }
}

// If something went wrong
echo json_encode(['status' => 'error', 'message' => 'Item not found or invalid index.']);
exit;
?>