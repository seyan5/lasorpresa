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
      <h3>Shopping cart</h3>
      
      <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <p>You have <?php echo count($_SESSION['cart']); ?> items in your cart</p>

        <?php foreach ($_SESSION['cart'] as $item): ?>
          <div class="cart-item">
            <?php if (isset($item['image']) && $item['image']): ?>
              <img src="../admin/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
            <?php else: ?>
              <img src="path/to/default-image.jpg" alt="No image available" width="50">
            <?php endif; ?>

            <div>
              <p><?php echo htmlspecialchars($item['name']); ?></p>
              <p><?php echo htmlspecialchars($item['quantity']); ?> pcs. Tulips</p>
            </div>

            <div class="quantity">
              <?php echo $item['quantity']; ?>
            </div>

            <div class="price">
              ₱<?php echo number_format($item['price'], 2); ?>
            </div>

            <button class="delete">🗑️</button>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Your cart is empty.</p>
      <?php endif; ?>
    </div>

    <div class="payment">
      <h3>Card Details</h3>
      <p>Mode of Payment</p>
      <div class="payment-options">
        <img src="path/to/gcash-logo.jpg" alt="GCash">
        <span>Cash On Pick Up</span>
      </div>

      <form>
        <label>Email</label>
        <input type="email" placeholder="E-mail" required>
        
        <label>Contact Number</label>
        <input type="text" placeholder="Number" required>
        
        <label>Account Name</label>
        <input type="text" placeholder="Account Name" required>
      </form>
      <hr>

      <div class="summary">
        <p>Subtotal <span>₱<?php 
          $subtotal = array_sum(array_map(function($item) {
            return $item['price'] * $item['quantity'];
          }, $_SESSION['cart']));
          echo number_format($subtotal, 2);
        ?></span></p>
        <p>Shipping <span>₱0</span></p>
        <p>Total <span>₱<?php echo number_format($subtotal, 2); ?></span></p>
      </div>

      <button class="checkout" onclick="checkout()">Checkout &gt;</button>
    </div>
  </div>

  <script>
    function checkout() {
      alert('Proceeding to checkout...');
    }
  </script>
</body>
</html>
