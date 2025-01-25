<?php 
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");
?>
<?php include('navuser.php'); ?>
<?php include('back.php'); ?>

<?php
  $stmt = $pdo->prepare("SELECT container_name, price FROM container WHERE container_id = ?");
  $stmt->execute([$customization['container_type']]);
  $container = $stmt->fetch(PDO::FETCH_ASSOC);

  $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = ?");
  $stmt->execute([$customization['container_color']]);
  $color = $stmt->fetch(PDO::FETCH_ASSOC);

  $container_name = $container['container_name'] ?? "Unknown Container";
  $container_price = $container['price'] ?? 0;
  $color_name = $color['color_name'] ?? "Unknown Color";

  $customization_total = $container_price;

 foreach ($customization['flowers'] as $flower) {
    $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = ?");
    $stmt->execute([$flower['flower_type']]);
    $flower_data = $stmt->fetch(PDO::FETCH_ASSOC);

    $flower_price = $flower_data['price'] ?? 0;
    $flower_quantity = $flower['num_flowers'] ?? 0;
    $customization_total += $flower_price * $flower_quantity;
 }
?>

<?php
                            $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = ?");
                            $stmt->execute([$flower['flower_type']]);
                            $flower_data = $stmt->fetch(PDO::FETCH_ASSOC);

                            $flower_name = $flower_data['name'] ?? "Unknown Flower";
                            $flower_price = $flower_data['price'] ?? 0;
                            $flower_quantity = $flower['num_flowers'] ?? 0;
                            ?>


<style>
  :root{
    --pink: #e84393;
    --main: #d0bcb3;
    --font: #d18276;
    --button: #d6a98f;
  }
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
  flex: 3;
  margin-top: -5rem;
  max-height: 50vh; /* Set a max height to limit the cart size */
  overflow-y: auto; /* Enable vertical scrolling */
  padding-right: 10px;
  scrollbar-width: thin; /* Slim scrollbar */
  scrollbar-color: #ccc transparent; /* Custom scrollbar color */
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
  background-color:rgb(233, 221, 204); /* Slightly lighter shade for contrast */
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

/* Delete button styles */
.delete {
  background: none;
  border: none;
  cursor: pointer;
  color: #aaa; /* Default icon color */
  font-size: 25px; /* Icon size */
  padding: 8px; /* Add some padding for better clickability */
  border-radius: 4px; /* Optional: rounded corners */
  transition: color 0.3s ease, background-color 0.3s ease; /* Smooth transitions */
  margin-right: 5rem;
}

.delete:hover {
  color: #ff0000; /* Change to red on hover */
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
  background-color: #333;
  color: #fff;
  padding: 15px; /* Larger button */
  font-size: 18px; /* Larger font size */
  border: none;
  border-radius: 6px; /* Smoother corners */
  cursor: pointer;
}

.checkout:hover {
  background-color: var(--button); /* Darker green for hover effect */
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
              <input type="checkbox" name="selected_customizations[]" value="<?php echo $index; ?>" id="customization-<?php echo $index; ?>" 
                        onchange="updateTotalPrice()">
                    <label for="customization-<?php echo $index; ?>">
                        <h4>Customization #<?php echo $index + 1; ?></h4>
                    </label>
                <!-- Product Image -->
                <img src="<?php echo htmlspecialchars(!empty($customization['expected_image']) ? 'uploads/' . $customization['expected_image'] : '/lasorpresa/images/default-image.jpg'); ?>" 
                  alt="<?php echo htmlspecialchars($item['name']); ?>" 
                  width="50">

                <div>
                  <!-- Product Name -->
                  <p>Container Type: <?php echo htmlspecialchars($container_name); ?> (₱<?php echo number_format($container_price, 2); ?>)</p>
                  <!-- Product Quantity -->
                  <p>Container Color:<?php echo htmlspecialchars($color_name); ?></p>
                  <p>Remarks: <?php echo htmlspecialchars($customization['remarks'] ?? 'None'); ?></p>
                </div>

                <!-- Quantity Controls -->

                <!-- Product Price -->
                <div class="price">
                  <p>Subtotal: ₱<span class="subtotal" data-price="<?php echo $customization_total; ?>"><?php echo number_format($customization_total, 2); ?></span></p>
                </div>

                <!-- Delete Item Form -->
                <form method="POST" action="cart-delete.php" id="delete-form-<?php echo $index; ?>">
                  <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                  <button type="button" class="delete" onclick="confirmDelete(<?php echo $index; ?>)">
                  <i class="fa fa-trash"></i>
                </button>                
                </form>
              </div>
              

            <?php endforeach; ?>
          <?php else: ?>
            <p>Your cart is empty.</p>
          <?php endif; ?>
        </div>
        

        <div class="payment">
          <h3>Summary</h3>
          <hr>
          <div class="summary">
          <p>Total Price: <span id="total-price">0.00</span></p>
          </div>

          <button type="submit" class="checkout">Proceed To Checkout</button>
        </div>
      </div>
      

      <script>
    function updateTotalPrice() {
        const checkboxes = document.querySelectorAll('input[name="selected_customizations[]"]:checked');
        let totalPrice = 0;

        checkboxes.forEach(checkbox => {
            const cartItem = checkbox.closest('.cart-item');
            const subtotalElement = cartItem.querySelector('.subtotal');
            const subtotal = parseFloat(subtotalElement.getAttribute('data-price'));
            totalPrice += subtotal;
        });

        document.getElementById('total-price').textContent = totalPrice.toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).replace('PHP', '');
    }
</script>

</body>

</style>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

</html>
