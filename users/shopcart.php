<?php 
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");
?>
<?php include('navuser.php'); ?>
<style>
  .container {
  display: flex;
  max-width: 1600px; /* Increased max-width for larger layout */
  margin: 40px auto; /* Larger margin for spacing */
  gap: 30px; /* Increased gap between cart and payment sections */
  margin-top: 25rem; /* Adjusted margin-top for better alignment */
}

.cart, .payment {
  border-radius: 12px; /* Smoother corners */
  padding: 30px; /* Larger padding for better spacing */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Added shadow for both */
}

.cart {
  flex: 3; /* Occupies more space for emphasis */
  margin-top: -5rem;
  max-height: calc(100vh - 10rem); /* Adjust height dynamically based on viewport */
  overflow-y: auto; /* Enables vertical scrolling when content exceeds max height */
  padding-right: 10px; /* Optional: Add padding to avoid cutting off content */
  scrollbar-width: thin; /* Optional: Slim scrollbar for modern browsers */
  scrollbar-color: #ccc transparent; /* Optional: Custom scrollbar colors */
}

/* Optional: Custom scrollbar styles for Webkit browsers (e.g., Chrome, Edge, Safari) */
.cart::-webkit-scrollbar {
  width: 8px; /* Scrollbar width */
}

.cart::-webkit-scrollbar-thumb {
  background: #ccc; /* Scrollbar thumb color */
  border-radius: 4px; /* Rounded corners */
}

.cart::-webkit-scrollbar-thumb:hover {
  background: #aaa; /* Hover color */
}

.cart::-webkit-scrollbar-track {
  background: transparent; /* Scrollbar track color */
}


.cart p{
  font-size: 15px; /* Larger font size */
  font-family: Verdana, Geneva, Tahoma, sans-serif;
  font-style: italic;
}

.payment {
  width: 400px; /* Fixed width for consistent size */
  max-width: 100%; /* Ensures it doesn’t overflow the container on smaller screens */
  background-color: #f5e6ec; /* Slightly lighter shade for contrast */
  border-radius: 12px; /* Smooth corners */
  padding: 30px; /* Ample padding for content spacing */
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for better visibility */
  align-self: flex-start; /* Aligns with the top of the container */
  position: sticky; /* Optional: keeps it visible on scroll */
  top: 20px; /* Ensures it sticks from the top */
}

.cart h2, .cart h3, .payment h3 {
  margin: 0;
  font-size: 25px; /* Increased font size */
  color: #333; /* Darker color for emphasis */
}

.cart a{
  font-size: 15px; /* Larger font size */
  font-family: Verdana, Geneva, Tahoma, sans-serif;
  font-style: italic;
}

.cart-item {
  display: flex;
  align-items: center;
  margin-top: 20px;
  border-bottom: 2px solid #ddd; /* Thicker border for clarity */
  padding-bottom: 15px; /* Extra padding for spacing */
}

.cart-item img {
  width: 100px; /* Larger image size */
  height: 100px; /* Larger image size */
  object-fit: cover;
  border-radius: 10px; /* Adjusted for a modern look */
}

.cart-item div {
  margin-left: 15px; /* Increased spacing */
  flex: 1;
}

.cart-item h4 {
  margin: 0;
  font-size: 15px; /* Increased font size */
}

.cart-item p {
  margin: 8px 0 0; /* Adjusted spacing */
  font-size: 20px; /* Larger font size */
  color: #666; /* Slightly lighter color */
}

.cart-item .price{
  font-size: 15px; /* Larger font size */
  font-family: Verdana, Geneva, Tahoma, sans-serif;
  font-style: italic;
  font-weight: 900;
}

.quantity input {
  width: 60px; /* Wider input field */
  text-align: center;
  font-size: 16px; /* Larger font size */
}

.delete {
  background: none;
  border: none;
  font-size: 18px; /* Larger font size */
  color: #aaa; /* Softer color */
  cursor: pointer;
}

.delete:hover {
  color: #ff0000; /* Highlight on hover */
}

.payment-options {
  display: flex;
  align-items: center;
  gap: 20px; /* Increased gap for better spacing */
  margin-bottom: 30px; /* More spacing from other sections */
}

.payment-options img {
  width: 60px; /* Larger icons */
}

form {
  display: flex;
  flex-direction: column;
  gap: 15px; /* Increased gap for better spacing */
}

form label {
  font-size: 16px; /* Larger font size */
  color: #444; /* Slightly darker color */
}

form input {
  padding: 12px; /* Increased padding */
  border: 2px solid #ccc; /* Thicker border */
  border-radius: 6px; /* Smoother corners */
  font-size: 16px; /* Larger font size */
}

form input:focus {
  border-color: #4caf50; /* Highlight border on focus */
  outline: none;
}

.summary {
  margin-top: 30px; /* Larger margin */
}

.summary p {
  display: flex;
  justify-content: space-between;
  margin: 15px 0; /* Larger spacing between rows */
  font-size: 16px; /* Larger font size */
  font-weight: bold; /* Bold text for emphasis */
}

.checkout {
  width: 100%;
  background-color: #4caf50;
  color: #fff;
  padding: 15px; /* Larger button */
  font-size: 18px; /* Larger font size */
  border: none;
  border-radius: 6px; /* Smoother corners */
  cursor: pointer;
}

.checkout:hover {
  background-color: #3b8b40; /* Darker green for hover effect */
}

</style>


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
    Swal.fire({
      title: 'Your cart is empty!',
      text: 'Would you like to browse products?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, take me there!',
      cancelButtonText: 'No, thanks',
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = "products.php";
      }
    });
  } else {
    if (!isLoggedIn) {
      Swal.fire({
        title: 'You need to log in!',
        text: 'Do you want to log in now?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, log me in!',
        cancelButtonText: 'No, I will log in later',
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "login.php";
        }
      });
    } else {
      window.location.href = "checkout.php";
    }
  }
}


    // Confirm deletion of cart item
    function confirmDelete(itemIndex) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You are about to remove this item from your cart.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, remove it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // If confirmed, submit the form
            document.getElementById('delete-form-' + itemIndex).submit();
        }
    });
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</html>
