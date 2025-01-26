<?php
include('auth.php'); // Ensure the admin is logged in
include('header.php');
include_once('conn.php');

// Set default sorting order
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'DESC';
$order_by = strtoupper($order_by) === 'ASC' ? 'ASC' : 'DESC';  // Default to DESC if not specified or invalid

// Fetch wishlist items and count of customers who have added each item to their wishlist
$stmt = $pdo->prepare("
    SELECT 
        p.p_id,
        p.name AS product_name,
        p.current_price,
        p.featured_photo,  -- Add the featured_photo column
        COUNT(w.p_id) AS product_count
    FROM wishlist w
    JOIN product p ON w.p_id = p.p_id
    GROUP BY w.p_id
    ORDER BY product_count $order_by
");

$stmt->execute();
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist Admin</title>
    <link rel="stylesheet" href="../../css/style.css"> <!-- Include your stylesheet -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .wishlist-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .wishlist-table th, .wishlist-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .wishlist-table th {
            background-color: #007BFF;
            color: white;
        }

        .wishlist-table td img {
            width: 100px; /* Adjust image size */
            height: auto;
            border-radius: 8px;
        }

        .wishlist-table td {
            vertical-align: middle;
        }

        .wishlist-table tr:hover {
            background-color: #f1f1f1;
        }

        .wishlist-table td .price {
            color: #28a745;
            font-weight: bold;
        }

        .wishlist-table td .count {
            background-color: #f8d7da;
            padding: 5px;
            border-radius: 4px;
            font-weight: bold;
        }
        .sort-arrow {
            cursor: pointer;
            padding-left: 5px;
        }

        .up-arrow::before {
            content: "\2191"; /* Unicode for up arrow */
        }

        .down-arrow::before {
            content: "\2193"; /* Unicode for down arrow */
        }

        /* Optional: style for sorted column */
        th.sorted-asc .up-arrow::before {
            color: #007bff;
        }

        th.sorted-desc .down-arrow::before {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Wishlist Items</h1>

        <?php if (count($wishlist_items) > 0) : ?>
            <table class="wishlist-table">
                <thead>
                    <tr>
                        <th>Featured Photo</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Count (Customers)</th>
                        <th>Sort<span 
                                class="sort-arrow <?= $order_by == 'ASC' ? 'up-arrow' : 'down-arrow' ?>" 
                                onclick="toggleSortOrder()"></span>
                        </th></th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wishlist_items as $item) : ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['featured_photo'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($item['featured_photo']) ?>" alt="Product Image">
                                <?php else: ?>
                                    <img src="uploads/default.jpg" alt="Default Image">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td class="price">₱<?= number_format($item['current_price'], 2) ?></td>
                            <td class="count"><?= $item['product_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p>No products found in the wishlist.</p>
        <?php endif; ?>
    </div>

    <script>
        // Function to toggle the sort order when the arrow is clicked
        function toggleSortOrder() {
            const currentOrder = '<?= $order_by ?>';
            const newOrder = currentOrder === 'ASC' ? 'DESC' : 'ASC';
            window.location.href = `?order_by=${newOrder}`;
        }
    </script>
</body>
</html>