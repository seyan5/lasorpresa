<?php
require 'header.php'; // Include any common setup, such as database connection

// Redirect to cart if the cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header('Location: shopcart.php');
    exit;
}

$total = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/checkout.css">
    <title>Checkout</title>
</head>
<body>
    <div class="header">
        <a href="shopcart.php" class="back-link">
            <span class="back-arrow">&lt;</span> Back to Cart
        </a>
    </div>

    <div class="container">
        <h2>Checkout</h2>
        <hr>

        <!-- Display Cart Summary -->
        <div class="summary">
            <h3>Order Summary</h3>
            <?php foreach ($_SESSION['cart'] as $item): ?>
                <div class="cart-item">
                    <img src="../admin/uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
                    <p><?php echo htmlspecialchars($item['name']); ?> (x<?php echo htmlspecialchars($item['quantity']); ?>)</p>
                    <p>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                </div>
            <?php endforeach; ?>
            <p><strong>Total: ₱<?php echo number_format($total, 2); ?></strong></p>
        </div>

        <!-- Shipping Details Form -->
        <form action="checkout-handler.php" method="POST">
    <h3>Shipping Information</h3>
    <label for="full_name">Full Name</label>
    <input type="text" id="full_name" name="full_name" required>

    <label for="address">Address</label>
    <textarea id="address" name="address" rows="3" required></textarea>

    <label for="city">City</label>
    <input type="text" id="city" name="city" required>

    <label for="postal_code">Postal Code</label>
    <input type="text" id="postal_code" name="postal_code" required>

    <label for="phone">Phone Number</label>
    <input type="text" id="phone" name="phone" required>

    <button type="submit" class="place-order">Place Order</button>
</form>

    </div>
</body>



</html>

