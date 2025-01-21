<?php
require 'conn.php'; // Include database connection

// Check if ecat_id and sort parameters are passed
$ecat_id = isset($_GET['ecat_id']) ? (int)$_GET['ecat_id'] : 'all';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'Default';  // Default is 'Default'

// Build the query based on ecat_id and sort parameter
try {
    // Base query
    $baseQuery = "
        SELECT p_id, name, featured_photo, current_price 
        FROM product
        WHERE is_active = 1 AND ecat_id IN (
            SELECT ecat_id 
            FROM end_category 
            WHERE mcat_id = 20
        )
    ";

    // Check for category and sorting
    if ($ecat_id === 'all') {
        // If 'all' is selected, just order by p_id
        $query = $baseQuery . " ORDER BY p_id DESC";
    } else {
        // If a specific category is selected
        $query = $baseQuery . " AND ecat_id = :ecat_id ORDER BY p_id DESC";
    }

    // Modify the query based on the sort parameter
    if ($sort === 'LowToHigh') {
        $query = str_replace("ORDER BY p_id DESC", "ORDER BY current_price ASC", $query);
    } elseif ($sort === 'HighToLow') {
        $query = str_replace("ORDER BY p_id DESC", "ORDER BY current_price DESC", $query);
    }

    // Prepare the statement
    $statement = $pdo->prepare($query);

    // Bind parameters if a specific category is selected
    if ($ecat_id !== 'all') {
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
                <a href="occasion-details.php?p_id=' . htmlspecialchars($product['p_id']) . '">
                    <img src="../admin/uploads/' . htmlspecialchars($product['featured_photo']) . '" alt="' . htmlspecialchars($product['name']) . '">
                    <h3>' . htmlspecialchars($product['name']) . '</h3>
                    <div class="price">₱' . number_format($product['current_price'], 2) . '</div>
                </a>
            </div>';
        }
    } else {
        // Fallback if no products found for the selected category
        echo '
        <div class="no-products text-center p-4 my-4">
            <i class="fas fa-box-open fa-3x text-secondary mb-3"></i>
            <h4 class="text-muted">No products found in this category</h4>
            <p>Try selecting a different category or check back later.</p>
        </div>';
    }
} catch (Exception $e) {
    // Error handling for database connection or query issues
    echo "<p>Error loading products: " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>

<style>
    .no-products {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .no-products i {
        color: #6c757d;
    }

    /* Grey out out-of-stock products */
    .out-of-stock {
        opacity: 0.5;
        pointer-events: none;
        background-color: #f0f0f0;
    }

    /* Out of Stock message styling */
    .out-of-stock-text {
        color: #999;
        font-weight: bold;
        text-align: center;
        font-size: 14px;
    }

    .out-of-stock a {
        pointer-events: none;
        color: #888;
    }

    .out-of-stock .price {
        color: #888;
    }
</style>
