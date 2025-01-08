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
    <link rel="stylesheet" href="../css/shopcart.css">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../css/checkout.css">
    <title>Checkout</title>
</head>

<body>
    <div class="header">
        <a href="index.php" class="back-link">
            <span class="back-arrow">&lt;</span> La Sorpresa Home Page
        <a href="shopcart.php" class="back-link">
            <span class="back-arrow">&lt;</span> Back to Cart
        </a>
    </div>

    <div class="container">
        <div class="cart">
            <hr>
            <h3>Order Summary</h3>

            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <p>You have <?php echo count($_SESSION['cart']); ?> items in your cart</p>

                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="cart-item">
                        <?php if (isset($item['image']) && $item['image']): ?>
                            <img src="../admin/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
                        <?php else: ?>
                            <img src="path/to/default-image.jpg" alt="No image available" width="50">
                        <?php endif; ?>

                        <div>
                            <p><?php echo htmlspecialchars($item['name']); ?></p>
                            <p><?php echo htmlspecialchars($item['quantity']); ?> pcs.</p>
                        </div>

                        <div class="quantity">
                            <?php echo $item['quantity']; ?>
                        </div>

                        <div class="price">
                            ₱<?php echo number_format($item['price'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
        <div class="payment">
            <h3>Summary</h3>
            <p></p>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>
            <label for="address">Mode of Payment:</label>
            <div>
                <input type="radio" id="gcash" name="payment_method" value="gcash" required>
                <label for="gcash">
                    <img src="../images/Gcash.png" alt="" width="50">
                    GCash
                </label>
            </div>
            <div>
                <input type="radio" id="cod" name="payment_method" value="cod" required>
                <label for="cod">
                    <img src="../images/cod.png" alt="" width="50">
                    Cash on Delivery (COD)
                </label>
            </div>

            <hr>

            <div class="summary">
                <p>Subtotal <span>₱<?php
                $subtotal = array_sum(array_map(function ($item) {
                    return $item['price'] * $item['quantity'];
                }, $_SESSION['cart']));
                echo number_format($subtotal, 2);
                ?></span></p>
                <p>Shipping <span>₱0</span></p>
                <p>
                <strong>Total:</strong>
                ₱<?php 
                echo number_format(array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $_SESSION['cart'])), 2); 
                ?>
            </p>
            </div>

            <button class="checkout" onclick="checkout()">Checkout &gt;</button>
        </div>
    </div>
    </div>
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
