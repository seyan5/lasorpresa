<?php
require 'header.php';

// Retrieve product details from the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
    $product_price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0.0;
    $addon = isset($_POST['addon']) ? $_POST['addon'] : '';  // Add-on selected by the user

    // Fetch the product image from the database
    $statement = $pdo->prepare("SELECT featured_photo FROM product WHERE p_id = :p_id");
    $statement->bindParam(':p_id', $product_id, PDO::PARAM_INT);
    $statement->execute();
    $product = $statement->fetch(PDO::FETCH_ASSOC);

    if ($product_id > 0 && $product_name && $product_price > 0) {
        // Initialize cart if not already initialized
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if the product already exists in the cart
        if (isset($_SESSION['cart'][$product_id])) {
            // Increment quantity if the product already exists
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            // Add new product to the cart with the image path and add-on
            $_SESSION['cart'][$product_id] = [
                'name' => $product_name,
                'price' => $product_price,
                'quantity' => 1,
                'image' => $product['featured_photo'], // Save image path in session
                'addon' => $addon // Save the selected add-on
            ];
        }

        echo json_encode([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart' => $_SESSION['cart']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product details!'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method!'
    ]);
}

// Return response in JSON format
header('Content-Type: application/json');
echo json_encode($response);
exit;
