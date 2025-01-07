<?php
require 'header.php';

// Check if ecat_id is passed
$ecat_id = isset($_GET['ecat_id']) ? (int)$_GET['ecat_id'] : 'all';

if ($ecat_id === 'all') {
    // Fetch all products if 'all' is selected
    $statement = $pdo->prepare("
        SELECT p_id, name, featured_photo, current_price 
        FROM product
        WHERE is_active = 1
        ORDER BY p_id DESC
    ");
} else {
    // Fetch products for the selected category
    $statement = $pdo->prepare("
        SELECT p_id, name, featured_photo, current_price 
        FROM product
        WHERE is_active = 1 AND ecat_id = :ecat_id
        ORDER BY p_id DESC
    ");
    $statement->bindParam(':ecat_id', $ecat_id, PDO::PARAM_INT);
}

$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC);

if ($products) {
    foreach ($products as $product) {
        echo '
        <div class="product" data-id="' . $product['p_id'] . '">
            <a href="product-details.php?p_id=' . $product['p_id'] . '">
                <img src="../admin/uploads/' . htmlspecialchars($product['featured_photo']) . '" alt="' . htmlspecialchars($product['name']) . '">
                <h3>' . htmlspecialchars($product['name']) . '</h3>
                <div class="price">$' . number_format($product['current_price'], 2) . '</div>
            </a>
        </div>';
    }
} else {
    echo "<p>No products found in this category.</p>";
}
?>
