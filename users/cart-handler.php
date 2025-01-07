<?php
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve product details from the request
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $product_name = isset($_POST['product_name']) ? $_POST['product_name'] : '';
    $product_price = isset($_POST['product_price']) ? (float)$_POST['product_price'] : 0.0;
    
    // Fetch the product's image from the database
    if ($product_id > 0 && $product_name && $product_price > 0) {
        // Fetch the product image from the database
        $stmt = $pdo->prepare("SELECT featured_photo FROM product WHERE p_id = :p_id");
        $stmt->bindParam(':p_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            $product_image = $product['featured_photo'];

            // Initialize cart if not already initialized
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Check if the product already exists in the cart
            if (isset($_SESSION['cart'][$product_id])) {
                // Increment quantity if the product already exists
                $_SESSION['cart'][$product_id]['quantity'] += 1;
            } else {
                // Add new product to the cart
                $_SESSION['cart'][$product_id] = [
                    'name' => $product_name,
                    'price' => $product_price,
                    'quantity' => 1,
                    'image' => $product_image  // Add image to cart item
                ];
            }

            echo json_encode([
                'success' => true,
                'message' => 'Product added to cart!',
                'cart' => $_SESSION['cart']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found!'
            ]);
        }
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
?>
