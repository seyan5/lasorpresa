<?php
session_start();
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}
$order_id = htmlspecialchars($_GET['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Thank You for Your Order!</h1>
    <p>Your order ID is: <strong><?php echo $order_id; ?></strong></p>
    <p>We will process your order soon.</p>
    <a href="products.php">Continue Shopping</a>
</body>
</html>
