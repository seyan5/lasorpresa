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
</head>
<body>
    <div class="product-details">
        <img src="../admin/uploads/<?php echo htmlspecialchars($product['featured_photo']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
        <div class="price">$<?php echo number_format($product['current_price'], 2); ?></div>
        <button onclick="addToCart(<?php echo $product['p_id']; ?>)">Add to Cart</button>
    </div>

    <script>
        function addToCart(productId) {
            alert("Product " + productId + " added to cart!");
        }
    </script>
</body>
</html>