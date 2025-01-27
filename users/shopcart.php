<?php
include("conn.php");
include('navuser.php');
include('back.php');
?>
<div class="ccart">
  <a href="customize-cart.php" class="button">View your Customization Cart</a>
</div>
<div class="container">
  <div class="cart">
    <hr>
    <h3>Shopping Cart</h3>
    <form id="cart-form" method="POST" action="checkout.php">
      <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
        <p>You have <?php echo count($_SESSION['cart']); ?> items in your cart.</p>
        <?php foreach ($_SESSION['cart'] as $index => $item): ?>
          <div class="cart-item">
  <input
    type="checkbox"
    class="cart-checkbox"
    name="selected_items[]"
    value="<?php echo $index; ?>"
    checked
    onchange="updateSummary()"
  >
  <img
    src="../admin/uploads/<?php echo !empty($item['image']) ? htmlspecialchars($item['image']) : 'default-image.jpg'; ?>"
    alt="<?php echo htmlspecialchars($item['name']); ?>"
    width="50"
  >
  <div>
    <p><?php echo htmlspecialchars($item['name']); ?></p>
    <div class="quantity-controls">
      <button type="button" class="btn-control" onclick="adjustQuantity(<?php echo $index; ?>, -1)">-</button>
      <span class="quantity-value" id="quantity-<?php echo $index; ?>"><?php echo htmlspecialchars($item['quantity']); ?></span>
      <button type="button" class="btn-control" onclick="adjustQuantity(<?php echo $index; ?>, 1)">+</button>
    </div>
  </div>
  <div class="price">
    ₱<span
      class="item-price"
      id="price-<?php echo $index; ?>"
      data-price="<?php echo $item['price']; ?>"
    >
      <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
    </span>
  </div>
  <button
    type="button"
    class="delete"
    onclick="confirmDelete(<?php echo $index; ?>)"
  >
    🗑️
  </button>
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
  const cartItems = document.querySelectorAll('.cart-item');
  let subtotal = 0;

  cartItems.forEach((item) => {
    const checkbox = item.querySelector('.cart-checkbox');
    if (checkbox.checked) {
      const priceElement = item.querySelector('.item-price');
      const priceText = priceElement.textContent.replace(/,/g, ''); // Remove commas
      const price = parseFloat(priceText);
      subtotal += price;
    }
  });

  // Update the subtotal and total in the summary
  document.getElementById('subtotal').textContent = `₱${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
  document.getElementById('total').textContent = `₱${subtotal.toLocaleString('en-US', { minimumFractionDigits: 2 })}`;
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

  function confirmDelete(index) {
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
      // Use AJAX to delete the item
      fetch('cart-delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `item_index=${index}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          // Remove the item from the DOM
          document.querySelectorAll('.cart-item')[index].remove();
          updateSummary(); // Recalculate totals dynamically
          Swal.fire('Deleted!', 'The item has been removed from your cart.', 'success');
        } else {
          Swal.fire('Error!', data.message || 'Something went wrong.', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error!', 'Failed to remove the item.', 'error');
      });
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
<script>
  function adjustQuantity(index, change) {
  const quantityElement = document.getElementById(`quantity-${index}`);
  const priceElement = document.getElementById(`price-${index}`);
  const itemPriceText = priceElement.getAttribute('data-price');
  const itemPrice = parseFloat(itemPriceText); // The raw price without formatting

  // Calculate new quantity
  let currentQuantity = parseInt(quantityElement.textContent);
  let newQuantity = currentQuantity + change;

  // Ensure quantity is at least 1
  if (newQuantity < 1) {
    Swal.fire({
      title: 'Minimum Quantity Reached',
      text: 'Quantity cannot be less than 1.',
      icon: 'info',
    });
    return;
  }

  // Update quantity in the DOM
  quantityElement.textContent = newQuantity;

  // Update item price in the DOM
  const newPrice = itemPrice * newQuantity;
  priceElement.textContent = newPrice.toLocaleString('en-US', { minimumFractionDigits: 2 });

  // Update the cart summary
  updateSummary();

  // Make an AJAX request to update the server-side cart
  fetch('cart-update.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `index=${index}&quantity=${newQuantity}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        Swal.fire('Error!', 'Failed to update the cart. Please try again.', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire('Error!', 'Failed to update the cart.', 'error');
    });
}

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
  margin: 40px auto;
  gap: 30px;
  margin-top: 0;
}

/* Cart and Payment sections */
.cart,
.payment {
  border-radius: 12px;
  padding: 30px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.cart {
  flex: 3;
  margin-top: -5rem;
  max-height: 50vh;
  overflow-y: auto;
  padding-right: 10px;
  scrollbar-width: thin;
  scrollbar-color: #ccc transparent;
}

/* Scrollbar styles */
.cart::-webkit-scrollbar {
  width: 8px;
}

.cart::-webkit-scrollbar-thumb {
  background: #ccc;
  border-radius: 4px;
}

.cart::-webkit-scrollbar-thumb:hover {
  background: #aaa;
}

.cart::-webkit-scrollbar-track {
  background: transparent;
}

.cart p {
  font-size: 15px;
  font-family: Verdana, Geneva, Tahoma, sans-serif;
  font-style: italic;
}

.payment {
  background-color: rgb(233, 221, 204);
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  margin: 20px auto;
}

/* Titles and Links */
.cart h2, .cart h3, .payment h3 {
  margin: 0;
  font-size: 25px;
  color: #333;
}

.cart a {
  font-size: 15px;
  font-family: Verdana, Geneva, Tahoma, sans-serif;
  font-style: italic;
}

.cart-checkbox {
  margin: 10px;
}

.cart-item {
  display: flex;
  align-items: center;
  margin-top: 20px;
  border-bottom: 2px solid #ddd;
  padding-bottom: 15px;
}

.cart-item img {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 10px;
}

.cart-item div {
  margin-left: 15px;
  flex: 1;
}

.cart-item h4 {
  margin: 0;
  font-size: 15px;
}

.cart-item p {
  margin: 8px 0 0;
  font-size: 20px;
  color: #666;
}

.cart-item .price {
  font-size: 15px;
  font-family: Verdana, Geneva, Tahoma, sans-serif;
  font-style: italic;
  font-weight: 900;
}

.quantity input {
  width: 60px;
  text-align: center;
  font-size: 16px;
}

/* Delete button */
.delete {
  background: none;
  border: none;
  cursor: pointer;
  color: #aaa;
  font-size: 25px;
  padding: 8px;
  border-radius: 4px;
  transition: color 0.3s ease, background-color 0.3s ease;
  margin-right: 5rem;
}

.delete:hover {
  color: #ff0000;
}

.payment-options {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
}

.payment-options img {
  width: 60px;
}

form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

form label {
  font-size: 16px;
  color: #444;
}

form input {
  padding: 12px;
  border: 2px solid #ccc;
  border-radius: 6px;
  font-size: 16px;
}

form input:focus {
  border-color: #4caf50;
  outline: none;
}

.summary {
  margin-top: 30px;
}

.summary p {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
  font-size: 16px;
  font-weight: bold;
}

.checkout {
  width: 100%;
  background-color: #333;
  color: #fff;
  padding: 15px;
  font-size: 18px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

.checkout:hover {
  background-color: var(--button);
}

/* Quantity controls */
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

/* Quantity controls alignment */
.quantity-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

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

.quantity-value {
  min-width: 24px;
  text-align: center;
  font-size: 16px;
}

/* Ccart positioning */
.ccart {
  margin-left: 132rem;
  margin-top: 20rem;
}

.ccart .button {
  background-color: #333;
  color: white;
  padding: 8px 16px;
  text-decoration: none;
  border-radius: 4px;
  font-size: 14px;
  display: inline-block;
  transition: background-color 0.3s ease;
}

.ccart .button:hover {
  background-color: var(--button);
}

/* Media Queries for Responsiveness */

@media (max-width: 1020px) {
  .container {
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .cart {
    flex: 2;
    max-height: 50vh;
    margin-top: 0;
  }
  
  .payment {
    max-width: 300px;
    margin: 20px auto;
  }

  .cart-item img {
    width: 80px;
    height: 80px;
  }

  .cart-item h4 {
    font-size: 14px;
  }

  .cart-item p {
    font-size: 18px;
  }

  .quantity input {
    width: 50px;
  }

  .summary p {
    font-size: 14px;
  }

  .checkout {
    font-size: 16px;
  }
}

@media (max-width: 780px) {
  .container {
    flex-direction: column;
    align-items: center;
    margin: 20px;
  }

  .cart,
  .payment {
    padding: 20px;
    width: 100%;
    max-width: 100%;
  }

  .cart-item {
    flex-direction: column;
    align-items: flex-start;
  }

  .cart-item img {
    width: 70px;
    height: 70px;
  }

  .cart-item div {
    margin-left: 0;
    margin-top: 10px;
  }

  .summary p {
    font-size: 13px;
  }

  .checkout {
    font-size: 16px;
    padding: 12px;
  }
}

@media (max-width: 430px) {
  .cart-item img {
    width: 60px;
    height: 60px;
  }

  .cart-item h4 {
    font-size: 13px;
  }

  .cart-item p {
    font-size: 16px;
  }

  .quantity input {
    width: 40px;
  }

  .summary p {
    font-size: 12px;
  }

  .checkout {
    font-size: 14px;
    padding: 10px;
  }
}



</style>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</html>