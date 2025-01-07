<?php
require 'header.php'; // Include database connection

// Check if ecat_id is passed
$ecat_id = isset($_GET['ecat_id']) ? (int)$_GET['ecat_id'] : 'all';

try {
    // Prepare SQL query based on whether 'all' or a specific category is selected
    if ($ecat_id === 'all') {
        // Fetch all products for ecat_id that is linked to mcat_id = 3
        $statement = $pdo->prepare("
            SELECT p_id, name, featured_photo, current_price 
            FROM product
            WHERE is_active = 1 AND ecat_id IN (
                SELECT ecat_id 
                FROM end_category 
                WHERE mcat_id = 3
            )
            ORDER BY p_id DESC
        ");
    } else {
        // Fetch products for the selected category within mcat_id = 3
        $statement = $pdo->prepare("
            SELECT p_id, name, featured_photo, current_price 
            FROM product
            WHERE is_active = 1 AND ecat_id = :ecat_id AND ecat_id IN (
                SELECT ecat_id 
                FROM end_category 
                WHERE mcat_id = 3
            )
            ORDER BY p_id DESC
        ");
        $statement->bindParam(':ecat_id', $ecat_id, PDO::PARAM_INT);
    }

    // Execute the query and fetch products
    $statement->execute();
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Output the products or a message if no products are found
    if ($products) {
        foreach ($products as $product) {
            echo '
            <div class="product" data-id="' . htmlspecialchars($product['p_id']) . '">
                <a href="product-details.php?p_id=' . htmlspecialchars($product['p_id']) . '">
                    <img src="../admin/uploads/' . htmlspecialchars($product['featured_photo']) . '" alt="' . htmlspecialchars($product['name']) . '">
                    <h3>' . htmlspecialchars($product['name']) . '</h3>
                    <div class="price">$' . number_format($product['current_price'], 2) . '</div>
                </a>
            </div>';
        }
    } else {
        // Fallback if no products found for the selected category
        echo "<p>No products found in this category.</p>";
    }
} catch (Exception $e) {
    // Error handling for database connection or query issues
    echo "<p>Error loading products: " . htmlspecialchars($e->getMessage()) . "</p>";
}


// Query to get add-ons related to mcat_id = 19 and their respective ecat_id values
$addonCategories = [
    'chocolate' => 17,  // ecat_id for chocolate
    'stufftoy' => 18,   // ecat_id for stuff toys
    'balloon' => 19     // ecat_id for balloons
];

// Array to hold options
$addonOptions = [];

foreach ($addonCategories as $addon => $ecat_id) {
    // Fetch the add-on name and related information
    $statement = $pdo->prepare("
        SELECT ecat_name 
        FROM end_category 
        WHERE mcat_id = 19 AND ecat_id = :ecat_id
    ");
    $statement->bindParam(':ecat_id', $ecat_id, PDO::PARAM_INT);
    $statement->execute();
    $addonData = $statement->fetch(PDO::FETCH_ASSOC);
    
    // Add to options if exists
    if ($addonData) {
        $addonOptions[$addon] = $addonData['ecat_name'];
    }
}
?>
