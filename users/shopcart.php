<?php
session_start();


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
      <h3>Shopping Cart</h3>
      
      <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <p>You have <?php echo count($_SESSION['cart']); ?> items in your cart.</p>

        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
          <div class="cart-item">
            <!-- Product Image -->
            <img src="../admin/uploads/<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'default-image.jpg'; ?>" 
                 alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">

            <div>
              <!-- Product Name -->
              <p><?php echo htmlspecialchars($item['name']); ?></p>
              <!-- Product Quantity -->
              <p><?php echo htmlspecialchars($item['quantity']); ?> pcs.</p>
            </div>

            <!-- Quantity -->
            <div class="quantity">
              <?php echo htmlspecialchars($item['quantity']); ?>
            </div>

            <!-- Product Price -->
            <div class="price">
              ₱<?php echo number_format($item['price'], 2); ?>
            </div>

            <!-- Delete Item Form -->
            <form method="POST" action="cart-delete.php" id="delete-form-<?php echo $index; ?>">
              <input type="hidden" name="item_index" value="<?php echo $index; ?>">
              <button type="button" class="delete" onclick="confirmDelete(<?php echo $index; ?>)">🗑️</button>
            </form>
          </div>
          

        <?php endforeach; ?>
      <?php else: ?>
        <p>Your cart is empty.</p>
      <?php endif; ?>
      <a href="addons.php">Want to get addons?</a>
    </div>
    

    <div class="payment">
      <h3>Summary</h3>
      <hr>
      <div class="summary">
        <p>Subtotal <span>₱<?php 
          $subtotal = isset($_SESSION['cart']) 
                      ? array_sum(array_map(function($item) {
                          return $item['price'] * $item['quantity'];
                        }, $_SESSION['cart'])) 
                      : 0;
          echo number_format($subtotal, 2);
        ?></span></p>
        <p>Shipping <span>₱0</span></p>
        <p>Total <span>₱<?php echo number_format($subtotal, 2); ?></span></p>
      </div>

      <button class="checkout" onclick="checkout()">Checkout &gt;</button>
    </div>
  </div>

  <script>
    // Check if the user is logged in
const isLoggedIn = <?php echo isset($_SESSION['customer']['cust_id']) ? 'true' : 'false'; ?>;

// Check if the cart is empty
const isCartEmpty = <?php echo isset($_SESSION['cart']) && count($_SESSION['cart']) > 0 ? 'false' : 'true'; ?>;

// Handle checkout action
function checkout() {
  if (isCartEmpty) {
    if (confirm("Your cart is empty. Would you like to browse products?")) {
      window.location.href = "products.php";
    }
  } else {
    if (!isLoggedIn) {
      if (confirm("You need to log in to proceed to checkout. Do you want to log in now?")) {
        window.location.href = "login.php";
      }
    } else {
      window.location.href = "checkout.php";
    }
  }
}

    // Confirm deletion of cart item
    function confirmDelete(itemIndex) {
      if (confirm("Are you sure you want to remove this item from your cart?")) {
        document.getElementById('delete-form-' + itemIndex).submit();
      }
    }
  </script>
</body>
</html>
