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
  gap: 40px;
  margin-top: 0;
  padding: 0 20px;
}

/* Cart and Payment sections */
.cart,
.payment {
  border-radius: 20px;
  padding: 40px;
  box-shadow: 
    0 20px 40px rgba(0, 0, 0, 0.08),
    0 8px 16px rgba(0, 0, 0, 0.04),
    inset 0 1px 0 rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.18);
  position: relative;
}

.cart {
  flex: 3;
  margin-top: -5rem;
  max-height: 50vh;
  overflow-y: auto;
  padding-right: 20px;
  background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
}

.cart::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: #d0bcb3;
  border-radius: 20px 20px 0 0;
}

/* Enhanced Scrollbar styles */
.cart::-webkit-scrollbar {
  width: 10px;
}

.cart::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, var(--button), var(--font));
  border-radius: 10px;
  border: 2px solid transparent;
  background-clip: content-box;
}

.cart::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, var(--font), var(--pink));
  background-clip: content-box;
}

.cart::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.05);
  border-radius: 10px;
}

.cart p {
  font-size: 15px;
  font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-style: italic;
  line-height: 1.6;
}

.payment {
  background: linear-gradient(145deg, rgb(233, 221, 204) 0%, rgba(233, 221, 204, 0.9) 100%);
  padding: 30px;
  border-radius: 20px;
  box-shadow: 
    0 15px 35px rgba(0, 0, 0, 0.1),
    0 5px 15px rgba(0, 0, 0, 0.05);
  width: 100%;
  max-width: 400px;
  margin: 20px auto;
  position: relative;
  overflow: hidden;
}

.payment::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: #d0bcb3;
  border-radius: 20px 20px 0 0;
}

/* Titles and Links */
.cart h2, .cart h3, .payment h3 {
  margin: 0 0 30px 0;
  font-size: 28px;
  color: #333;
  font-weight: 700;
  letter-spacing: -0.5px;
  position: relative;
  padding-bottom: 15px;
}

.cart h2::after, .cart h3::after, .payment h3::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 3px;
  background: #d0bcb3;
  border-radius: 2px;
}

.cart a {
  font-size: 15px;
  font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-style: italic;
  color: var(--font);
  text-decoration: none;
  transition: all 0.3s ease;
  position: relative;
}

.cart a::after {
  content: '';
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 0;
  height: 2px;
  background: #d0bcb3;
  transition: width 0.3s ease;
}

.cart a:hover::after {
  width: 100%;
}

.cart a:hover {
  color: var(--pink);
}

.cart-checkbox {
  margin: 15px;
  position: relative;
}

.cart-checkbox input[type="checkbox"] {
  width: 20px;
  height: 20px;
  accent-color: var(--pink);
  cursor: pointer;
}

.cart-item {
  display: flex;
  align-items: center;
  margin-top: 25px;
  border-bottom: 1px solid rgba(221, 221, 221, 0.6);
  padding: 20px 0;
  transition: all 0.3s ease;
  border-radius: 12px;
  position: relative;
}

.cart-item:hover {
  background: rgba(232, 67, 147, 0.02);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
  border-bottom-color: rgba(232, 67, 147, 0.3);
}

.cart-item::before {
  content: '';
  position: absolute;
  left: -40px;
  top: 0;
  bottom: 0;
  width: 3px;
  background: #d0bcb3;
  border-radius: 2px;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.cart-item:hover::before {
  opacity: 1;
}

.cart-item img {
  width: 110px;
  height: 110px;
  object-fit: cover;
  border-radius: 16px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 2px solid rgba(255, 255, 255, 0.8);
}

.cart-item:hover img {
  transform: scale(1.05);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
}

.cart-item div {
  margin-left: 20px;
  flex: 1;
}

.cart-item h4 {
  margin: 0 0 8px 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
  line-height: 1.4;
  letter-spacing: -0.2px;
}

.cart-item p {
  margin: 8px 0 0;
  font-size: 20px;
  color: #666;
  line-height: 1.5;
}

.cart-item .price {
  font-size: 16px;
  font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  font-style: italic;
  font-weight: 700;
  color: var(--font);
  background: linear-gradient(90deg, var(--font), var(--pink));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.quantity input {
  width: 70px;
  text-align: center;
  font-size: 16px;
  padding: 8px;
  border: 2px solid rgba(208, 188, 179, 0.3);
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.8);
  transition: all 0.3s ease;
}

.quantity input:focus {
  border-color: var(--pink);
  outline: none;
  box-shadow: 0 0 0 3px rgba(232, 67, 147, 0.1);
}

/* Enhanced Delete button */
.delete {
  background: none;
  border: none;
  cursor: pointer;
  color: #aaa;
  font-size: 24px;
  padding: 12px;
  border-radius: 50%;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin-right: 5rem;
  position: relative;
  overflow: hidden;
}

.delete::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  background: rgba(255, 0, 0, 0.1);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  transition: all 0.3s ease;
}

.delete:hover::before {
  width: 100%;
  height: 100%;
}

.delete:hover {
  color: #ff0000;
  background: rgba(255, 0, 0, 0.05);
  transform: scale(1.1);
}

.delete:active {
  transform: scale(0.95);
}

.payment-options {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 12px;
  backdrop-filter: blur(5px);
}

.payment-options img {
  width: 60px;
  height: auto;
  border-radius: 8px;
  transition: transform 0.3s ease;
  cursor: pointer;
}

.payment-options img:hover {
  transform: scale(1.1);
}

form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

form label {
  font-size: 16px;
  color: #444;
  font-weight: 600;
  margin-bottom: 5px;
}

form input {
  padding: 15px;
  border: 2px solid rgba(208, 188, 179, 0.3);
  border-radius: 12px;
  font-size: 16px;
  background: rgba(255, 255, 255, 0.8);
  transition: all 0.3s ease;
}

form input:focus {
  border-color: var(--pink);
  outline: none;
  box-shadow: 0 0 0 3px rgba(232, 67, 147, 0.1);
  background: rgba(255, 255, 255, 0.95);
}

.summary {
  margin-top: 30px;
  padding: 25px;
  background: rgba(255, 255, 255, 0.4);
  border-radius: 16px;
  backdrop-filter: blur(10px);
}

.summary p {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.summary p:last-child {
  border-top: 2px solid var(--button);
  padding-top: 15px;
  font-size: 18px;
  color: var(--font);
}

.checkout {
  width: 100%;
  background: linear-gradient(135deg, #333 0%, #555 100%);
  color: #fff;
  padding: 18px;
  font-size: 18px;
  font-weight: 600;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  position: relative;
  overflow: hidden;
}

.checkout::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.checkout:hover::before {
  left: 100%;
}

.checkout:hover {
  background: linear-gradient(135deg, var(--button) 0%, var(--font) 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(214, 169, 143, 0.4);
}

.checkout:active {
  transform: translateY(1px);
}

/* Quantity controls */
.quantity-form {
  display: flex;
  align-items: center;
  gap: 10px;
}

.quantity-form button {
  background: linear-gradient(135deg, #f0f0f0 0%, #e0e0e0 100%);
  border: 1px solid rgba(208, 188, 179, 0.3);
  padding: 8px 12px;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 600;
}

.quantity-form button:hover {
  background: linear-gradient(135deg, var(--button) 0%, var(--font) 100%);
  color: white;
  transform: scale(1.05);
}

.quantity-form span {
  margin: 0 10px;
  font-weight: bold;
  color: var(--font);
  font-size: 16px;
}

/* Enhanced Quantity controls alignment */
.quantity-controls {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(255, 255, 255, 0.5);
  padding: 8px;
  border-radius: 12px;
  backdrop-filter: blur(5px);
}

.btn-control {
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  color: white;
  border: none;
  padding: 8px 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
}

.btn-control:hover {
  background: linear-gradient(135deg, #0056b3 0%, #003d82 100%);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.4);
}

.btn-control:active {
  transform: translateY(1px);
}

.quantity-value {
  min-width: 30px;
  text-align: center;
  font-size: 16px;
  font-weight: 600;
  color: var(--font);
}

/* Enhanced Cart positioning */
.ccart {
  margin-left: 132rem;
  margin-top: 20rem;
}

.ccart .button {
  background: linear-gradient(135deg, #333 0%, #555 100%);
  color: white;
  padding: 12px 20px;
  text-decoration: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  display: inline-block;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  position: relative;
  overflow: hidden;
}

.ccart .button::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.6s ease;
}

.ccart .button:hover::before {
  left: 100%;
}

.ccart .button:hover {
  background: linear-gradient(135deg, var(--button) 0%, var(--font) 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(214, 169, 143, 0.4);
}

/* Modern animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.cart-item {
  animation: fadeInUp 0.5s ease forwards;
}

/* Media Queries for Responsiveness */
@media (max-width: 1020px) {
  .container {
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
  }
  
  .cart {
    flex: 2;
    max-height: 50vh;
    margin-top: 0;
    padding: 30px;
  }
  
  .payment {
    max-width: 350px;
    margin: 20px auto;
    padding: 25px;
  }

  .cart-item img {
    width: 90px;
    height: 90px;
  }

  .cart-item h4 {
    font-size: 16px;
  }

  .cart-item p {
    font-size: 18px;
  }

  .quantity input {
    width: 60px;
  }

  .summary p {
    font-size: 15px;
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
    gap: 20px;
  }

  .cart,
  .payment {
    padding: 25px;
    width: 100%;
    max-width: 100%;
    border-radius: 16px;
  }

  .cart-item {
    flex-direction: column;
    align-items: flex-start;
    padding: 15px 0;
  }

  .cart-item img {
    width: 80px;
    height: 80px;
  }

  .cart-item div {
    margin-left: 0;
    margin-top: 15px;
  }

  .summary p {
    font-size: 14px;
  }

  .checkout {
    font-size: 16px;
    padding: 15px;
  }

  .delete {
    margin-right: 2rem;
  }
}

@media (max-width: 430px) {
  .cart,
  .payment {
    padding: 20px;
    border-radius: 12px;
  }

  .cart-item img {
    width: 70px;
    height: 70px;
  }

  .cart-item h4 {
    font-size: 15px;
  }

  .cart-item p {
    font-size: 16px;
  }

  .quantity input {
    width: 50px;
    padding: 6px;
  }

  .summary p {
    font-size: 13px;
  }

  .checkout {
    font-size: 14px;
    padding: 12px;
  }

  .payment-options {
    gap: 15px;
    padding: 15px;
  }

  .payment-options img {
    width: 50px;
  }
}
</style>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</html>