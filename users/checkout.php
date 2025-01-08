<?php
require 'header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/shopcart.css">
    <title>Shopping Cart</title>
</head>

<body>
    <div class="header">
        <a href="index.php" class="back-link">
            <span class="back-arrow">&lt;</span> La Sorpresa Home Page
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

    <!-- JavaScript -->
    <script src="js/checkout.js"></script>
</body>

</html>