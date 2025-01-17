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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <!-- Font Awesome and Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    
    <link rel="stylesheet" href="../css/prod-details.css?v=1.0">
</head>

<body>
    <header>
        <a href="products.php" class="back">← Back to Products</a>
        <a href="shopcart.php" class="back"> Cart</a>

    </header>
    <main>
        <div class="pic">
            <img src="../admin/uploads/<?php echo htmlspecialchars($product['featured_photo']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
    
        </div>

        <!-- Reviews Section -->
                
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

        </section>
    </main>
    
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
            alert("Product added to cart successfully!");
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


</html>