<?php
require 'header.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['id']);
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

      <?php foreach ($_SESSION['cart'] as $index => $item): ?>
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

          <!-- Delete Button with Confirmation -->
          <form method="POST" action="cart-delete.php" id="delete-form-<?php echo $index; ?>">
            <input type="hidden" name="item_index" value="<?php echo $index; ?>">
            <button type="button" class="delete" onclick="confirmDelete(<?php echo $index; ?>)">🗑️</button>
          </form>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </div>

    <div class="payment">
      <h3>Summary</h3>
      <p></p>
      <div class="payment-options">
        <img src="" alt="">
        <span></span>
      </div>

      
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
  </div>

  <script>
    // Pass login status from PHP to JavaScript
    const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;

    function checkout() {
      if (!isLoggedIn) {
        // Prompt user to log in if not logged in
        const loginConfirm = confirm("You need to log in to proceed to checkout. Do you want to log in now?");
        if (loginConfirm) {
          window.location.href = "../login.php"; // Redirect to login page
        }
      } else {
        // Redirect to checkout page if logged in
        window.location.href = "checkout.php";
      }
    }
  </script>

<script>
  // JavaScript function to confirm deletion
  function confirmDelete(itemIndex) {
    // Ask the user for confirmation
    const confirmation = confirm("Are you sure you want to remove this item from your cart?");
    
    if (confirmation) {
      // If confirmed, submit the corresponding form
      document.getElementById('delete-form-' + itemIndex).submit();
    }
  }
</script>
</body>
</html>
