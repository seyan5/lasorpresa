<?php
require 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>
    <main>
        <h1>Your Shopping Cart</h1>
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p>
                <strong>Total:</strong>
                $<?php 
                echo number_format(array_sum(array_map(function($item) {
                    return $item['price'] * $item['quantity'];
                }, $_SESSION['cart'])), 2); 
                ?>
            </p>
            <button onclick="checkout()">Checkout</button>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </main>
    <script>
        function checkout() {
            alert('Proceeding to checkout...');
        }
    </script>
</body>
</html>


