<?php
require 'conn.php'; // Include database connection

// Get the product ID from the query parameter
$p_id = isset($_GET['p_id']) ? (int) $_GET['p_id'] : 0;

if ($p_id) {
    // Fetch product details from the database
    $statement = $pdo->prepare("
    SELECT p_id, name, featured_photo, current_price, description, quantity
    FROM product 
    WHERE p_id = :p_id
");
$statement->bindParam(':p_id', $p_id, PDO::PARAM_INT);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<p>Product not found.</p>";
    exit;
}

$product_quantity = $product['quantity']; // Get product quantity
} else {
    echo "<p>Invalid product ID.</p>";
    exit;
}

  // Fetch reviews for the product
  $reviewStmt = $pdo->prepare("
  SELECT r.review, r.rating, r.created_at, c.cust_name
  FROM reviews r
  LEFT JOIN customer c ON r.customer_id = c.cust_id
  WHERE r.product_id = :p_id
  ORDER BY r.created_at DESC
");
$reviewStmt->bindParam(':p_id', $p_id, PDO::PARAM_INT);
$reviewStmt->execute();
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include('navuser.php'); ?>
<link rel="stylesheet" href="../css/prod-details.css?v=1.1">

    <main>
        <div class="pic">
            <img src="../admin/uploads/<?php echo htmlspecialchars($product['featured_photo']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <section>
        </div>
                

            </section>
        <main>

            <!-- Right Sidebar -->
            <div class="sidebar">
                <h2>Cart</h2>
                <div class="cart-item">
                    <img src="../admin/uploads/<?php echo htmlspecialchars($product['featured_photo']); ?>"
                        alt="Cart Item">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <div class="price">₱<?php echo number_format($product['current_price'], 2); ?></div>
                </div>


                <div class="total">
                    <span>Subtotal:</span>
                    <span>₱<?php echo number_format($product['current_price'], 2); ?></span>
                </div>


                <button id="addToCartButton" data-id="<?php echo $product['p_id']; ?>"
        data-name="<?php echo htmlspecialchars($product['name']); ?>"
        data-price="<?php echo htmlspecialchars($product['current_price']); ?>"
        onclick="addToCart(<?php echo $product['p_id']; ?>)"
        <?php if ($product_quantity == 0) echo 'disabled'; ?>>
    Add to Cart
</button>

<?php if ($product_quantity == 0): ?>
    <p>This product is out of stock.</p>
<?php endif; ?>

            </div>
        </main>

        <section id="reviews">
    <h3>Customer Reviews</h3>
    <?php if (empty($reviews)): ?>
        <p>No reviews yet. Be the first to review this product!</p>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="review">
                <p><strong>Reviewer:</strong> <?php echo htmlspecialchars($review['cust_name']) ?: 'Anonymous'; ?></p>
                <p><strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']) ?: 'No rating'; ?></p>
                <p><strong>Review:</strong> <?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                <p><small>Reviewed on <?php echo htmlspecialchars($review['created_at']); ?></small></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
 
    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const productQuantity = <?php echo $product_quantity; ?>;
        const addToCartButton = document.getElementById('addToCartButton');
        
        if (productQuantity === 0) {
            addToCartButton.disabled = true;
            alert('This product is out of stock!');
        }
    });

    function addToCart(productName) {
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
        }
    </script>
    <script>
        document.getElementById('addToCartButton').addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = this.getAttribute('data-price');

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
                        alert(data.message);
                        window.location.href = 'shopcart.php';  
                        console.log('Cart:', data.cart); // Debugging: Log cart content
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
    </script>

</body>

<style>
    /* General Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f9f9f9;
        /* Light background */
        color: #333;
        /* Dark text for contrast */
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #ffffff;
        padding: 15px 50px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
    }

    header a {
        text-decoration: none;
        color: #ff6f61;
        font-size: 16px;
    }

    main {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 50px;
        gap: 30px;
    }

    .product-details {
        display: flex;
        flex-direction: column;
        max-width: 400px;
        text-align: left;
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .product-details img {
        width: 100%;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .product-details h1 {
        font-size: 24px;
        margin-bottom: 10px;
        color: #333;
    }

    .product-details p {
        font-size: 16px;
        color: #666;
        margin-bottom: 20px;
    }

    .sidebar {
        display: flex;
        flex-direction: column;
        max-width: 350px;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
        font-size: 18px;
        color: #333;
        margin-bottom: 20px;
    }

    .sidebar .cart-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .cart-item img {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        margin-right: 15px;
    }

    .cart-item h4 {
        font-size: 16px;
        color: #333;
    }

    .cart-item .price {
        font-size: 14px;
        color: #666;
        margin-left: auto;
    }

    .sidebar .addons {
        margin-bottom: 20px;
    }

    .sidebar select {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .sidebar .total {
        display: flex;
        justify-content: space-between;
        font-size: 16px;
        color: #333;
        margin-top: 20px;
        font-weight: 600;
    }

    .sidebar button {
        width: 100%;
        padding: 15px;
        font-size: 16px;
        background-color: #ff6f61;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 20px;
    }

    .sidebar button:hover {
        background-color: #e65a50;
    }
</style>

</html>