<?php
require_once('conn.php');


// Check if the user is logged in by checking the session variable
if (!isset($_SESSION['customer'])) {
    echo json_encode(['success' => false, 'message' => 'User is not logged in.']);
    exit;
}

// Get customer ID from the session
$cust_id = $_SESSION['customer']['cust_id'];

// Proceed with the rest of the wishlist logic
$product_id = isset($_POST['p_id']) ? (int) $_POST['p_id'] : 0;

if ($product_id && $cust_id) {
    $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE cust_id = :cust_id AND p_id = :p_id");
    $stmt->execute(['cust_id' => $cust_id, 'p_id' => $product_id]);

    if ($stmt->rowCount() === 0) {
        $insertStmt = $pdo->prepare("INSERT INTO wishlist (cust_id, p_id) VALUES (:cust_id, :p_id)");
        $insertStmt->execute(['cust_id' => $cust_id, 'p_id' => $product_id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'This product is already in your wishlist.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid product or customer ID.']);
}
