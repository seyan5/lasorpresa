
<?php
require 'header.php'; // Include database connection

// Get the product ID from the query parameter
$p_id = isset($_GET['p_id']) ? (int)$_GET['p_id'] : 0;

if ($p_id) {
    // Fetch product details from the database
    $statement = $pdo->prepare("
        SELECT p_id, name, featured_photo, current_price, description 
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
} else {
    echo "<p>Invalid product ID.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="">
</head>
<body>
    <header>
        <a href="products.php" class="back">← Back to Products</a>
        <a href="cart.php" class="back"> Cart</a>

    </header>
    <main>
        <div class="product-details">
            <img src="../admin/uploads/<?php echo htmlspecialchars($product['featured_photo']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p><?php echo htmlspecialchars($product['description']); ?></p>
            <div class="price">$<?php echo number_format($product['current_price'], 2); ?></div>
            <button id="addToCartButton" 
        data-id="<?php echo $product['p_id']; ?>" 
        data-name="<?php echo htmlspecialchars($product['name']); ?>" 
        data-price="<?php echo number_format($product['current_price'], 2); ?>">
    Add to Cart
</button>


        </div>
    </main>
    <script>
        function addToCart(productId) {
            alert("Product " + productId + " added to cart!");
        }
    </script>
    <script>
    document.getElementById('addToCartButton').addEventListener('click', function() {
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
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

header .back {
    text-decoration: none;
    color: #007bff;
    margin: 20px;
    display: inline-block;
}

.product-details {
    max-width: 800px;
    margin: 50px auto;
    text-align: center;
}

.product-details img {
    max-width: 100%;
    height: auto;
}

.product-details h1 {
    font-size: 2rem;
    margin: 20px 0;
}

.product-details p {
    font-size: 1.2rem;
    margin: 20px 0;
}

.product-details .price {
    font-size: 1.5rem;
    color: green;
    margin: 20px 0;
}

.product-details button {
    padding: 10px 20px;
    font-size: 1rem;
    background-color: blue;
    color: white;
    border: none;
    cursor: pointer;
}

.product-details button:hover {
    background-color: darkblue;
}

</style>
</html>

