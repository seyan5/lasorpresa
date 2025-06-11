<?php
require_once('conn.php');

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
session_start();
include("../admin/inc/config.php");

if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    // Delete the wishlist item from the database
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id = :item_id");
    $stmt->execute(['item_id' => $item_id]);

    echo json_encode(['success' => true]);
}
?>


<?php include('back.php'); ?>
<style>
  .wishlist p{
    font-size: 18px; 
    color: #666; 
    margin-top: 10px;
    margin-bottom: 20px;
  }

  .container {
    display: flex;
    max-width: 1600px; 
    margin: 40px auto; 
    gap: 40px; 
    margin-top: 20rem; 
    padding: 0 20px;
  }

  .wishlist {
    flex: 1; 
    border-radius: 20px; 
    padding: 40px; 
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    box-shadow: 
      0 20px 40px rgba(0, 0, 0, 0.08),
      0 8px 16px rgba(0, 0, 0, 0.04),
      inset 0 1px 0 rgba(255, 255, 255, 0.7);
    max-height: calc(75vh - 10rem); 
    overflow-y: auto; 
    border: 1px solid rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(10px);
    position: relative;
  }

  .wishlist::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(76, 175, 80, 0.3), transparent);
    border-radius: 20px 20px 0 0;
  }

  .wishlist::-webkit-scrollbar {
    width: 8px;
  }

  .wishlist::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
    border-radius: 10px;
  }

  .wishlist::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #4CAF50, #45a049);
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: content-box;
  }

  .wishlist::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #45a049, #3d8b40);
    background-clip: content-box;
  }

  .wishlist h2, .wishlist h3 {
    margin: 0 0 30px 0;
    font-size: 28px; 
    color: #333; 
    font-weight: 700;
    letter-spacing: -0.5px;
    position: relative;
    padding-bottom: 15px;
  }

  .wishlist h2::after, .wishlist h3::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: #d0bcb3;
    border-radius: 2px;
  }

  .wishlist-item {
    display: flex;
    align-items: center;
    margin-top: 25px;
    border-bottom: 1px solid rgba(221, 221, 221, 0.6); 
    padding: 20px 0; 
    transition: all 0.3s ease;
    border-radius: 12px;
    position: relative;
  }

  .wishlist-item:hover {
    background: rgba(76, 175, 80, 0.02);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
    border-bottom-color: rgba(76, 175, 80, 0.3);
  }

  .wishlist-item::before {
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

  .wishlist-item:hover::before {
    opacity: 1;
  }

  .wishlist-item img {
    width: 110px; 
    height: 110px; 
    object-fit: cover;
    border-radius: 16px; 
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 2px solid rgba(255, 255, 255, 0.8);
  }

  .wishlist-item:hover img {
    transform: scale(1.05);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18);
  }

  .wishlist-item div {
    margin-left: 20px; 
    flex: 1;
  }

  .wishlist-item h4 {
    margin: 0 0 8px 0;
    font-size: 18px; 
    font-weight: 600;
    color: #333;
    line-height: 1.4;
    letter-spacing: -0.2px;
  }

  .wishlist-item p {
    margin: 8px 0 0; 
    font-size: 20px; 
    color: #666; 
    line-height: 1.5;
  }

  .wishlist-item .price {
    font-size: 20px; 
    font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-style: italic;
    font-weight: 700;
    color:rgb(0, 0, 0);
    background: linear-gradient(90deg,rgb(0, 0, 0),rgb(0, 0, 0));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .wishlist a {
    font-size: 15px; 
    font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-style: italic;
    color: #4CAF50;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
  }

  .wishlist a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: #d0bcb3;
    transition: width 0.3s ease;
  }

  .wishlist a:hover::after {
    width: 100%;
  }

  .wishlist a:hover {
    color: #d0bcb3;
  }

  /* Enhanced Add to Cart Button */
  #addToCartButton {
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 12px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 
      0 4px 15px rgba(76, 175, 80, 0.3),
      0 2px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
  }

  #addToCartButton::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s ease;
  }

  #addToCartButton:hover::before {
    left: 100%;
  }

  #addToCartButton ion-icon {
    margin-right: 10px;
    font-size: 18px;
    transition: transform 0.3s ease;
  }

  #addToCartButton:hover {
    background: linear-gradient(135deg, #45a049 0%, #3d8b40 100%);
    transform: translateY(-2px);
    box-shadow: 
      0 8px 25px rgba(76, 175, 80, 0.4),
      0 4px 12px rgba(0, 0, 0, 0.15);
  }

  #addToCartButton:hover ion-icon {
    transform: scale(1.1);
  }

  #addToCartButton:active {
    transform: translateY(1px);
    box-shadow: 
      0 2px 8px rgba(76, 175, 80, 0.3),
      0 1px 4px rgba(0, 0, 0, 0.1);
  }

  #addToCartButton:disabled {
    background: linear-gradient(135deg, #cccccc 0%, #b8b8b8 100%);
    cursor: not-allowed;
    box-shadow: none;
    transform: none;
  }

  /* Enhanced Remove Button */
  .remove {
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

  .remove::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.3s ease;
  }

  .remove:hover::before {
    width: 100%;
    height: 100%;
  }

  .remove:hover {
    color: #ff0000;
    transform: scale(1.1);
  }

  .remove:active {
    transform: scale(0.95);
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

  .wishlist-item {
    animation: fadeInUp 0.5s ease forwards;
  }

  /* Responsive improvements */
  @media (max-width: 768px) {
    .container {
      margin-top: 10rem;
      padding: 0 15px;
      gap: 20px;
    }
    
    .wishlist {
      padding: 25px;
      border-radius: 16px;
    }
    
    .wishlist-item img {
      width: 80px;
      height: 80px;
    }
    
    .remove {
      margin-right: 2rem;
    }
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
        data-stock="<?php echo $item['quantity']; ?>"
        data-item-id="<?php echo $item['id']; ?>"> 
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
  function addToCart(productId, productName, productPrice, productStock, itemId) {
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

            // Now remove the item from the wishlist after it is added to the cart
            removeFromWishlist(itemId);
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function removeFromWishlist(itemId) {
    // Send request to remove item from wishlist
    fetch('wishlist-remove.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_id=${itemId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item from the DOM (effectively removing it from the wishlist)
            document.getElementById('wishlist-item-' + itemId).remove();
        } else {
            console.log("Error removing item from wishlist.");
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
        const itemId = this.getAttribute('data-item-id');  // The item ID from the wishlist

        console.log(`Product ID: ${productId}, Name: ${productName}, Price: ${productPrice}, Stock: ${productStock}`);  // Debugging the data passed

        addToCart(productId, productName, productPrice, productStock, itemId);  // Call addToCart with product data
    });
});

function removeFromWishlist(itemId) {
    // Send request to remove item from wishlist
    fetch('wishlist-remove.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `item_id=${itemId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the item from the DOM (effectively removing it from the wishlist)
            document.getElementById('wishlist-item-' + itemId).remove();
        } else {
            console.log("Error removing item from wishlist.");
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
        const itemId = this.getAttribute('data-item-id');  // The item ID from the wishlist

        console.log(`Product ID: ${productId}, Name: ${productName}, Price: ${productPrice}, Stock: ${productStock}`);  // Debugging the data passed

        addToCart(productId, productName, productPrice, productStock, itemId);  // Call addToCart with product data
    });
});


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
