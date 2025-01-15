<?php
require 'conn.php'; // Include database connection

// Check if ecat_id is passed
$ecat_id = isset($_GET['ecat_id']) ? (int)$_GET['ecat_id'] : 'all';

// Check for sorting order parameter
$sort_order = isset($_GET['sort']) ? $_GET['sort'] : 'Default';
$order_by_clause = "";

if ($sort_order === 'LowToHigh') {
    $order_by_clause = "ORDER BY current_price ASC";
} elseif ($sort_order === 'HighToLow') {
    $order_by_clause = "ORDER BY current_price DESC";
} else {
    // Default sorting: out-of-stock products will be listed last
    $order_by_clause = "ORDER BY quantity DESC, p_id DESC";  // Sort by quantity (in-stock first), then by p_id
}

try {
    // Prepare SQL query based on whether 'all' or a specific category is selected
    if ($ecat_id === 'all') {
        $statement = $pdo->prepare("
            SELECT p_id, name, featured_photo, current_price, quantity 
            FROM product
            WHERE is_active = 1 AND ecat_id IN (
                SELECT ecat_id 
                FROM end_category 
                WHERE mcat_id = 3
            )
            $order_by_clause
        ");
    } else {
        $statement = $pdo->prepare("
            SELECT p_id, name, featured_photo, current_price, quantity 
            FROM product
            WHERE is_active = 1 AND ecat_id = :ecat_id AND ecat_id IN (
                SELECT ecat_id 
                FROM end_category 
                WHERE mcat_id = 3
            )
            $order_by_clause
        ");
        $statement->bindParam(':ecat_id', $ecat_id, PDO::PARAM_INT);
    }

    $statement->execute();
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($products) {
        foreach ($products as $product) {
            // Check if product is out of stock
            $isOutOfStock = $product['quantity'] == 0;
            
            // Add class if the product is out of stock
            $outOfStockClass = $isOutOfStock ? 'out-of-stock' : '';

            echo '
            <div class="product ' . $outOfStockClass . '" data-id="' . htmlspecialchars($product['p_id']) . '">
                <a href="product-details.php?p_id=' . htmlspecialchars($product['p_id']) . '">
                    <img src="../admin/uploads/' . htmlspecialchars($product['featured_photo']) . '" alt="' . htmlspecialchars($product['name']) . '">
                    <h3>' . htmlspecialchars($product['name']) . '</h3>
                    <div class="price">₱' . number_format($product['current_price'], 2) . '</div>
                </a>';
    
                // Show out of stock message if the product is out of stock
                if ($isOutOfStock) {
                    echo '<p class="out-of-stock-text">Out of Stock</p>';
                }
    
            echo '</div>';
        }
    } else {
        // Enhanced message design for no products found
        echo '
        <div class="no-products text-center p-4 my-4">
            <i class="fas fa-box-open fa-3x text-secondary mb-3"></i>
            <h4 class="text-muted">No products found in this category</h4>
            <p>Try selecting a different category or check back later.</p>
        </div>';
    }
} catch (Exception $e) {
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
