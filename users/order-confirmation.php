<?php
require 'header.php'; // Ensure this file includes the database connection

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = htmlspecialchars($_GET['order_id']);

// Fetch order details
$statement = $pdo->prepare("
    SELECT 
        p.name AS product_name,
        p.featured_photo AS product_image,
        oi.price AS product_price,
        oi.quantity AS product_quantity
    FROM order_items oi
    JOIN product p ON oi.product_id = p.p_id
    WHERE oi.order_id = :order_id
");
$statement->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$statement->execute();
$order_items = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .product-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        .summary {
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Thank You for Your Order!</h1>
    <p>Your order ID is: <strong><?php echo $order_id; ?></strong></p>
    <p>We will process your order soon.</p>

    <h2>Order Details</h2>
    <?php if ($order_items): ?>
        <table>
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price (Each)</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td>
                            <img src="../admin/uploads/<?php echo htmlspecialchars($item['product_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                 class="product-image">
                        </td>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['product_quantity']); ?></td>
                        <td>₱<?php echo number_format($item['product_price'], 2); ?></td>
                        <td>₱<?php echo number_format($item['product_price'] * $item['product_quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="summary">
            Total Amount: ₱<?php 
                $total = array_sum(array_map(function($item) {
                    return $item['product_price'] * $item['product_quantity'];
                }, $order_items));
                echo number_format($total, 2);
            ?>
        </div>
    <?php else: ?>
        <p>No items found for this order.</p>
    <?php endif; ?>

    <a href="products.php">Continue Shopping</a>
</body>
</html>
