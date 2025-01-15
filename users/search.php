<?php
// Include the configuration file to connect to the database
include("conn.php");

if (isset($_GET['search'])) {
    // Sanitize user input to prevent SQL injection
    $searchTerm = htmlspecialchars($_GET['search']);

    // Query the database to find products matching the search term
    $stmt = $pdo->prepare("SELECT * FROM product WHERE name LIKE :searchTerm LIMIT 10");
    $stmt->execute(['searchTerm' => "%$searchTerm%"]);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if any products were found
    if (empty($products)) {
        echo "<p>No products found.</p>";
    } else {
        // Loop through the results and display them
        foreach ($products as $product) {
            ?>
            <div class="product-item">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p><?= htmlspecialchars($product['description']) ?></p>
                <!-- Use the correct column name for price (e.g., product_price, current_price) -->
                <p>Price: $<?= number_format($product['price'] ?? 0, 2) ?></p>
            </div>
            <?php
        }
    }
} else {
    echo "<p>Please enter a search term.</p>";
}
?>
