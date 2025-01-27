<?php
require_once('conn.php');


// Ensure the user is logged in
if (!isset($_SESSION['customer'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Get the logged-in user's customer ID
$cust_id = $_SESSION['customer']['cust_id'];

// Check if item ID is provided in the request
if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    // Delete the item from the wishlist for the logged-in user
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id = :item_id AND cust_id = :cust_id");
    $stmt->execute([
        'item_id' => $item_id,
        'cust_id' => $cust_id
    ]);

    // Optionally, add a success message or redirect
    header("Location: wishlist.php"); // Redirect back to the wishlist page
    exit();
} else {
    // If no item_id is provided, redirect to wishlist page with an error
    header("Location: wishlist.php?error=missing_item");
    exit();
}
?>
