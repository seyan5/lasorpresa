<?php
session_start();

include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");


if (!isset($_SESSION['customer']['cust_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: products.php");
    exit();
}

$subtotal = array_sum(array_map(function($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart']));
$shipping = 50; // Example shipping fee
$total = $subtotal + $shipping;

// Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate order ID
    $order_id = uniqid('ORD');

    // Save order details to the database (example only; adjust to your DB structure)
    // Assuming a database pdoection $pdo
  
    $stmt = $pdo->prepare("INSERT INTO orders (order_id, cust_id, total_price) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $order_id, $_SESSION['customer']['cust_id'], $total);
    $stmt->execute();

    foreach ($_SESSION['cart'] as $item) {
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, p_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }
  

    // Clear cart after checkout
    unset($_SESSION['cart']);
    header("Location: confirmation.php?order_id=$order_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
    <div class="header">
        <a href="cart.php" class="back-link">
            <span class="back-arrow">&lt;</span> Back to Cart
        </a>
    </div>

    <div class="container">
        <h3>Checkout</h3>
        <hr>
        <h4>Order Summary</h4>
        <div class="summary">
            <p>Subtotal: <span>₱<?php echo number_format($subtotal, 2); ?></span></p>
            <p>Shipping: <span>₱<?php echo number_format($shipping, 2); ?></span></p>
            <p><strong>Total:</strong> <span>₱<?php echo number_format($total, 2); ?></span></p>
        </div>

        <h4>Shipping Information</h4>
        <form method="POST">
            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required>
            <label for="payment-method">Payment Method:</label>
            <select id="payment-method" name="payment_method" required>
                <option value="cod">Cash on Delivery</option>
                <option value="card">Credit/Debit Card</option>
            </select>
            <button type="submit" class="place-order">Place Order</button>
        </form>
    </div>
</body>
</html>
