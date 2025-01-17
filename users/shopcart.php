<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- css -->
     <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/main.css">
  <link rel="stylesheet" href="../css/shopcart.css?">
  <title>Shopping Cart</title>
</head>
<body>
  <header>

  <input type="checkbox" name="" id="toggler">
    <label for="toggler" class="fas fa-bars"></label>

    <!-- <a href="#" class="logo">Flower<span>.</span></a> -->
    <img src="../images/logo.png" alt="" class="logos" href="">
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="#about">About</a>
        <div class="prod-dropdown">
            <a href="" onclick="toggleDropdown()">Products</a>
                <div class="prod-menu" id="prodDropdown">
                    <a href="products.php">Flowers</a>
                    <a href="occasion.php">Occasion</a>
                    <a href="addons.php">Addons</a>
                </div>
        </div>
        <a href="#review">Review</a>
        <a href="#contacts">Contacts</a>
        <a href="customization.php">Customize</a>

    </nav>
     
    <div class="icons">
    <a href="#" class="fas fa-heart"></a>
    <a href="shopcart.php" class="fas fa-shopping-cart"></a>
    <div class="user-dropdown">
        <a href="#" class="fas fa-user" onclick="toggleDropdown()"></a>
        <div class="dropdown-menu" id="userDropdown">
            <?php if (isset($_SESSION['customer'])): ?>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?></p>
                <hr>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>


  </header>
      <!-- <div class="header">
        <a href="index.php" class="back-link">
          <span class="back-arrow">&lt;</span> La Sorpresa Home Page
        </a>
      </div> -->

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

                <!-- Quantity Controls -->
                <div class="quantity">
    <form method="POST" action="cart-update.php" class="quantity-form">
        <input type="hidden" name="item_index" value="<?php echo $index; ?>">
        <div class="quantity-controls">
            <button type="submit" name="action" value="decrease" class="btn-control">-</button>
            <span class="quantity-value"><?php echo htmlspecialchars($item['quantity']); ?></span>
            <button type="submit" name="action" value="increase" class="btn-control">+</button>
        </div>
    </form>
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

<style>
  .quantity-form {
  display: flex;
  align-items: center;
}

.quantity-form button {
  background-color: #f0f0f0;
  border: 1px solid #ccc;
  padding: 5px;
  cursor: pointer;
}

.quantity-form span {
  margin: 0 10px;
  font-weight: bold;
}
</style>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Close the dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.fa-user')) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    };
</script>
<style>
  <style>
    /* Container to align items side by side */
    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 8px; /* Adjust spacing between controls */
    }

    /* Button styles */
    .btn-control {
        background-color: #007bff;
        color: black;
        border: none;
        padding: 5px 10px;
        font-size: 16px;
        cursor: pointer;
        border-radius: 4px;
    }

    .btn-control:hover {
        background-color: #0056b3;
    }

    /* Quantity display style */
    .quantity-value {
        min-width: 24px;
        text-align: center;
        font-size: 16px;
    }

    /* Optional: Form styling */
    .quantity-form {
        margin: 0;
    }
</style>

</style>

</html>
