<?php
ob_start();
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");
?>
<?php include('navuser.php'); ?>

<?php
// Fetch wishlist items for the logged-in customer
if (isset($_SESSION['customer'])) {
    $cust_id = $_SESSION['customer']['cust_id'];
    $stmt = $pdo->prepare("SELECT w.*, p.name, p.featured_photo, p.current_price, p.quantity FROM wishlist w
  JOIN product p ON w.p_id = p.p_id
  WHERE w.cust_id = :cust_id");
    $stmt->execute(['cust_id' => $cust_id]);
    $wishlistItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $wishlistItems = []; // No items if no customer is logged in
}
?>

<?php

  

?>
<?php include('back.php'); ?>
<style>
  .container {
    display: flex;
    max-width: 1600px; 
    margin: 40px auto; 
    gap: 30px; 
    margin-top: 20rem; 
  }

  .wishlist {
    flex: 1; 
    border-radius: 12px; 
    padding: 30px; 
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
    max-height: calc(75vh - 10rem); 
    overflow-y: auto; 
  }

  .wishlist h2, .wishlist h3 {
    margin: 0;
    font-size: 25px; 
    color: #333; 
  }

  .wishlist-item {
    display: flex;
    align-items: center;
    margin-top: 20px;
    border-bottom: 2px solid #ddd; 
    padding-bottom: 15px; 
  }

  .wishlist-item img {
    width: 100px; 
    height: 100px; 
    object-fit: cover;
    border-radius: 10px; 
  }

  .wishlist-item div {
    margin-left: 15px; 
    flex: 1;
  }

  .wishlist-item h4 {
    margin: 0;
    font-size: 15px; 
  }

  .wishlist-item p {
    margin: 8px 0 0; 
    font-size: 20px; 
    color: #666; 
  }

  .wishlist-item .price {
    font-size: 15px; 
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-style: italic;
    font-weight: 900;
  }

  .remove {
    background: none;
    border: none;
    font-size: 18px; 
    color: #aaa; 
    cursor: pointer;
  }

  .remove:hover {
    color: #ff0000; 
  }

  .wishlist a {
    font-size: 15px; 
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-style: italic;
  }


/* Style for the Add to Cart button */
#addToCartButton {
  display: flex;
  align-items: center;
  background-color: #4CAF50; /* Green background */
  color: white;
  padding: 5px 10px;
  border-radius: 5px;
  border: none;
  font-size: 13px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Add spacing between icon and text */
#addToCartButton ion-icon {
  margin-right: 8px;
  font-size: 20px;
}

/* Hover effect */
#addToCartButton:hover {
  background-color: #45a049; /* Slightly darker green */
  transform: scale(1.05);
}

/* Active (clicked) state */
#addToCartButton:active {
  transform: scale(0.98); /* Slight shrink effect */
}

/* Disabled state (when out of stock or other conditions) */
#addToCartButton:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.remove {
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

.remove:hover {
  color: #ff0000; /* Change to red on hover */
}

</style>


<div class="container">
  <div class="wishlist">
    <h3>Your Wishlist</h3>
    
    <?php if (!empty($wishlistItems)): ?>
      <p>You have <?php echo count($wishlistItems); ?> items in your wishlist.</p>

      <?php foreach ($wishlistItems as $index => $item): ?>
        <div class="wishlist-item">
  <img src="../admin/uploads/<?php echo !empty($item['featured_photo']) ? htmlspecialchars($item['featured_photo']) : 'default-image.jpg'; ?>" 
       alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
  
  <div>
    <p><?php echo htmlspecialchars($item['name']); ?></p>
  </div>

  <div class="price">
    ₱<?php echo number_format($item['current_price'], 2); ?>
  </div>

  <!-- Add to Cart Button -->
  <button id="addToCartButton" 
          data-id="<?php echo $item['p_id']; ?>" 
          data-name="<?php echo htmlspecialchars($item['name']); ?>" 
          data-price="<?php echo $item['current_price']; ?>"
          data-stock="<?php echo $item['quantity']; ?>"> 
        Add to Cart
  </button>

  <!-- Remove Button -->
  <form method="POST" action="wishlist-remove.php" id="remove-form-<?php echo $index; ?>">
    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>"> <!-- Assuming `id` is the primary key -->
    <button type="button" class="remove" onclick="confirmRemove(<?php echo $index; ?>)"><i class="fa fa-trash"></i></button>
  </form>
</div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Your wishlist is empty.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  function confirmRemove(itemIndex) {
    Swal.fire({
      title: 'Are you sure?',
      text: 'You are about to remove this item from your wishlist.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, remove it!',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('remove-form-' + itemIndex).submit();
      }
    });
  }
</script>

<script>
  // Updated addToCart function to take dynamic product details
  function addToCart(productId, productName, productPrice, productStock) {
  if (typeof productStock === 'undefined' || productStock === null || productStock <= 0) {
    Swal.fire({
      title: 'Out of Stock!',
      text: `Sorry, the product "${productName}" is currently out of stock.`,
      icon: 'error',
      confirmButtonText: 'Okay'
    });
    return; // Prevent adding to the cart
  }

  // Proceed with adding to cart
  Swal.fire({
    title: 'Product added to cart!',
    text: `Do you want to go to your cart to review your items?`,
    icon: 'success',
    showCancelButton: true,
    confirmButtonText: 'Yes, go to cart',
    cancelButtonText: 'No, continue shopping'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = 'shopcart.php';  // Redirect to cart
    }
  });

  // Send AJAX request to add product to cart
  fetch('cart-handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `product_id=${productId}&product_name=${encodeURIComponent(productName)}&product_price=${productPrice}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log('Cart:', data.cart); // Debugging: Log cart content
    } else {
      alert(data.message);
    }
  })
  .catch(error => {
    console.error('Error:', error);
  });
}


// Ensure the add to cart button works for each product in the wishlist
document.querySelectorAll('#addToCartButton').forEach(button => {
    button.addEventListener('click', function(event) {
        event.preventDefault();  // Prevent page reload or form submission

        const productId = this.getAttribute('data-id');
        const productName = this.getAttribute('data-name');
        const productPrice = this.getAttribute('data-price');
        const productStock = this.getAttribute('data-stock');  // Stock quantity from data-attribute

        console.log(`Product ID: ${productId}, Name: ${productName}, Price: ${productPrice}, Stock: ${productStock}`);  // Debugging the data passed

        addToCart(productId, productName, productPrice, productStock);  // Call addToCart with product data
    });
});

</script>



</body>
</html>
