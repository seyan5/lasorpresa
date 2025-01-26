<?php
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");
include('navuser.php');
include('back.php');
?>

<div class="container">
  <div class="cart">
    <hr>
    <h3>Shopping Cart</h3>
    <form id="cart-form" method="POST" action="checkout.php">
      <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <p>You have <?php echo count($_SESSION['cart']); ?> items in your cart.</p>
        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
          <div class="cart-item">
            <input type="checkbox" class="cart-checkbox" name="selected_items[]" value="<?php echo $index; ?>" checked onchange="updateSummary()">
            <img
              src="../admin/uploads/<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'default-image.jpg'; ?>"
              alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
            <div>
              <p><?php echo htmlspecialchars($item['name']); ?></p>
              <p><?php echo htmlspecialchars($item['quantity']); ?> pcs.</p>
            </div>
            <div class="price">
              ₱<span class="item-price" data-price="<?php echo $item['price'] * $item['quantity']; ?>">
                <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
              </span>
            </div>
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
      <p>Subtotal <span id="subtotal">₱0.00</span></p>
      <p>Shipping <span>₱0</span></p>
      <p>Total <span id="total">₱0.00</span></p>
      <button type="submit" class="checkout">Checkout Selected &gt;</button>
    </div>
  </div>
</form> <!-- Properly closing the form tag here -->
</div>

</body>

<script>
  // Update subtotal and total dynamically when items are checked or unchecked
  function updateSummary() {
    const checkboxes = document.querySelectorAll('.cart-checkbox:checked');
    let subtotal = 0;

    checkboxes.forEach((checkbox) => {
      const priceElement = checkbox.parentElement.querySelector('.item-price');
      const price = parseFloat(priceElement.getAttribute('data-price'));
      subtotal += price;
    });

    // Update the subtotal and total in the summary
    document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('total').textContent = `₱${subtotal.toFixed(2)}`;
  }

  // Initialize the summary when the page loads
  document.addEventListener('DOMContentLoaded', () => {
    updateSummary();
  });

  // Existing functions (not modified)
  const isLoggedIn = <?php echo isset($_SESSION['customer']['cust_id']) ? 'true' : 'false'; ?>;
  const isCartEmpty = <?php echo isset($_SESSION['cart']) && count($_SESSION['cart']) > 0 ? 'false' : 'true'; ?>;

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
        document.getElementById('delete-form-' + itemIndex).submit();
      }
    });
  }

  function toggleDropdown() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.classList.toggle('show');
  }

  window.onclick = function (event) {
    if (!event.target.matches('.fa-user')) {
      const dropdown = document.getElementById('userDropdown');
      if (dropdown && dropdown.classList.contains('show')) {
        dropdown.classList.remove('show');
      }
    }
  };
</script>



<style>
  :root {
    --pink: #e84393;
    --main: #d0bcb3;
    --font: #d18276;
    --button: #d6a98f;
  }

  .container {
    display: flex;
    max-width: 1600px;
    /* Increased max-width for larger layout */
    margin: 40px auto;
    /* Larger margin for spacing */
    gap: 30px;
    /* Increased gap between cart and payment sections */
    margin-top: 25rem;
    /* Adjusted margin-top for better alignment */
  }

  .cart,
  .payment {
    border-radius: 12px;
    /* Smoother corners */
    padding: 30px;
    /* Larger padding for better spacing */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    /* Added shadow for both */
  }

  .cart {
    flex: 3;
    margin-top: -5rem;
    max-height: 50vh;
    /* Set a max height to limit the cart size */
    overflow-y: auto;
    /* Enable vertical scrolling */
    padding-right: 10px;
    scrollbar-width: thin;
    /* Slim scrollbar */
    scrollbar-color: #ccc transparent;
    /* Custom scrollbar color */
  }

  /* Optional: Custom scrollbar styles for Webkit browsers (e.g., Chrome, Edge, Safari) */
  .cart::-webkit-scrollbar {
    width: 8px;
    /* Scrollbar width */
  }

  .cart::-webkit-scrollbar-thumb {
    background: #ccc;
    /* Scrollbar thumb color */
    border-radius: 4px;
    /* Rounded corners */
  }

  .cart::-webkit-scrollbar-thumb:hover {
    background: #aaa;
    /* Hover color */
  }

  .cart::-webkit-scrollbar-track {
    background: transparent;
    /* Scrollbar track color */
  }


  .cart p {
    font-size: 15px;
    /* Larger font size */
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-style: italic;
  }

  .payment {
    width: 400px;
    /* Fixed width for consistent size */
    max-width: 100%;
    /* Ensures it doesn’t overflow the container on smaller screens */
    background-color: rgb(233, 221, 204);
    /* Slightly lighter shade for contrast */
    border-radius: 12px;
    /* Smooth corners */
    padding: 30px;
    /* Ample padding for content spacing */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    /* Subtle shadow for better visibility */
    align-self: flex-start;
    /* Aligns with the top of the container */
    position: sticky;
    /* Optional: keeps it visible on scroll */
    top: 20px;
    /* Ensures it sticks from the top */
  }

  .cart h2,
  .cart h3,
  .payment h3 {
    margin: 0;
    font-size: 25px;
    /* Increased font size */
    color: #333;
    /* Darker color for emphasis */
  }

  .cart a {
    font-size: 15px;
    /* Larger font size */
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-style: italic;
  }

  .cart-item {
    display: flex;
    align-items: center;
    margin-top: 20px;
    border-bottom: 2px solid #ddd;
    /* Thicker border for clarity */
    padding-bottom: 15px;
    /* Extra padding for spacing */
  }

  .cart-item img {
    width: 100px;
    /* Larger image size */
    height: 100px;
    /* Larger image size */
    object-fit: cover;
    border-radius: 10px;
    /* Adjusted for a modern look */
  }

  .cart-item div {
    margin-left: 15px;
    /* Increased spacing */
    flex: 1;
  }

  .cart-item h4 {
    margin: 0;
    font-size: 15px;
    /* Increased font size */
  }

  .cart-item p {
    margin: 8px 0 0;
    /* Adjusted spacing */
    font-size: 20px;
    /* Larger font size */
    color: #666;
    /* Slightly lighter color */
  }

  .cart-item .price {
    font-size: 15px;
    /* Larger font size */
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-style: italic;
    font-weight: 900;
  }

  .quantity input {
    width: 60px;
    /* Wider input field */
    text-align: center;
    font-size: 16px;
    /* Larger font size */
  }

  /* Delete button styles */
  .delete {
    background: none;
    border: none;
    cursor: pointer;
    color: #aaa;
    /* Default icon color */
    font-size: 25px;
    /* Icon size */
    padding: 8px;
    /* Add some padding for better clickability */
    border-radius: 4px;
    /* Optional: rounded corners */
    transition: color 0.3s ease, background-color 0.3s ease;
    /* Smooth transitions */
    margin-right: 5rem;
  }

  .delete:hover {
    color: #ff0000;
    /* Change to red on hover */
  }


  .payment-options {
    display: flex;
    align-items: center;
    gap: 20px;
    /* Increased gap for better spacing */
    margin-bottom: 30px;
    /* More spacing from other sections */
  }

  .payment-options img {
    width: 60px;
    /* Larger icons */
  }

  form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    /* Increased gap for better spacing */
  }

  form label {
    font-size: 16px;
    /* Larger font size */
    color: #444;
    /* Slightly darker color */
  }

  form input {
    padding: 12px;
    /* Increased padding */
    border: 2px solid #ccc;
    /* Thicker border */
    border-radius: 6px;
    /* Smoother corners */
    font-size: 16px;
    /* Larger font size */
  }

  form input:focus {
    border-color: #4caf50;
    /* Highlight border on focus */
    outline: none;
  }

  .summary {
    margin-top: 30px;
    /* Larger margin */
  }

  .summary p {
    display: flex;
    justify-content: space-between;
    margin: 15px 0;
    /* Larger spacing between rows */
    font-size: 16px;
    /* Larger font size */
    font-weight: bold;
    /* Bold text for emphasis */
  }

  .checkout {
    width: 100%;
    background-color: #333;
    color: #fff;
    padding: 15px;
    /* Larger button */
    font-size: 18px;
    /* Larger font size */
    border: none;
    border-radius: 6px;
    /* Smoother corners */
    cursor: pointer;
  }

  .checkout:hover {
    background-color: var(--button);
    /* Darker green for hover effect */
  }

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

  /* Container to align items side by side */
  .quantity-controls {
    display: flex;
    align-items: center;
    gap: 8px;
    /* Adjust spacing between controls */
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</html>