<?php
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'];

    // Check if the customer is logged in
    if (isset($_SESSION['customer'])) {
        $custId = $_SESSION['customer']['cust_id'];

        // Check if the product exists in the database
        $stmt = $pdo->prepare("SELECT * FROM product WHERE p_id = :item_id");
        $stmt->execute(['item_id' => $itemId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Initialize cart if not set
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Check if the product is already in the cart
            $isInCart = false;
            foreach ($_SESSION['cart'] as $cartItem) {
                if ($cartItem['id'] === $itemId) {
                    $isInCart = true;
                    break;
                }
            }

            if (!$isInCart) {
                // Add product to cart session
                $_SESSION['cart'][] = [
                    'id' => $product['p_id'],
                    'name' => $product['name'],
                    'price' => $product['current_price'],
                    'quantity' => 1, // Set initial quantity to 1
                    'image' => $product['featured_photo'] // Add image URL to the session cart data
                ];

                // Redirect to the cart page
                header('Location: shopcart.php');
                exit;
            } else {
                // If the item is already in the cart, inform the user
                $_SESSION['error'] = 'Product is already in the cart.';
                header('Location: wishlist.php');
                exit;
            }
        } else {
            // If product is not found, show an error message
            $_SESSION['error'] = 'Product not found.';
            header('Location: wishlist.php');
            exit;
        }
    } else {
        // If no customer is logged in, redirect to the login page
        header('Location: login.php');
        exit;
    }
}
?>

<script>
    document.getElementById('addToCartButton').addEventListener('click', function () {
    const productId = this.getAttribute('data-id');
    const productName = this.getAttribute('data-name');
    const productPrice = this.getAttribute('data-price');

    // Send AJAX request to add product to cart
    fetch('wishlist-add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_id=${productId}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Item added to cart:', data);
    })
    .catch(error => {
        console.error('Error adding item to cart:', error);
    });
});

</script>