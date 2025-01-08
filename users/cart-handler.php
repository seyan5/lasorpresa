<?php
require 'header.php';  // Ensure session_start() is called here

// Retrieve product details from the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $product_name = isset($_POST['product_name']) ? htmlspecialchars($_POST['product_name']) : ''; // Sanitize product name
    $product_price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0.0;

    // Validate inputs
    if ($product_id <= 0 || empty($product_name) || $product_price <= 0.0) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product details!'
        ]);
        exit;
    }

    // Fetch the product image from the database
    $statement = $pdo->prepare("SELECT featured_photo FROM product WHERE p_id = :p_id");
    $statement->bindParam(':p_id', $product_id, PDO::PARAM_INT);
    $statement->execute();
    $product = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found!'
        ]);
        exit;
    }

    // Initialize cart if not already initialized
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the product already exists in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Increment quantity if the product already exists
        $_SESSION['cart'][$product_id]['quantity']++;
    } else {
        // Add new product to the cart with the image path
        $_SESSION['cart'][$product_id] = [
            'name' => $product_name,
            'price' => $product_price,
            'quantity' => 1,
            'image' => !empty($product['featured_photo']) ? $product['featured_photo'] : 'images/default-image.jpg' // Set default image if no product image
        ];
    }

    // Calculate total quantity and price for the cart
    $total_quantity = array_sum(array_column($_SESSION['cart'], 'quantity'));
    $total_price = array_sum(array_map(function($item) {
        return $item['price'] * $item['quantity'];
    }, $_SESSION['cart']));

    // Return updated cart with total quantity and price
    echo json_encode([
        'success' => true,
        'message' => 'Product added to cart successfully!',
        'cart' => $_SESSION['cart'],
        'total_quantity' => $total_quantity,
        'total_price' => $total_price
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method!'
    ]);
}
?>
