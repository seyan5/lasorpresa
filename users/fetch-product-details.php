<?php
// fetch_product_details.php

require 'header.php'; // Ensure you connect to your database

// Get the product ID
$p_id = isset($_GET['p_id']) ? (int)$_GET['p_id'] : 0;

if ($p_id) {
    // Fetch product details based on product ID
    $statement = $pdo->prepare("
        SELECT p_id, name, featured_photo, current_price, description 
        FROM product
        WHERE p_id = :p_id
    ");
    $statement->bindParam(':p_id', $p_id, PDO::PARAM_INT);
    $statement->execute();

    $product = $statement->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Set header for JSON response
        header('Content-Type: application/json');
        
        // Return product details as JSON
        echo json_encode($product);
    } else {
        // Return an empty JSON if no product is found
        echo json_encode([]);
    }
}
?>
