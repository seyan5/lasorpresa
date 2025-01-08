<?php
session_start();
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
        .order-summary {
            margin: 20px 0;
        }
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .order-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            margin-right: 15px;
        }
        .order-item .details {
            flex-grow: 1;
        }
        .order-item .price {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Thank You for Your Order!</h1>
    <p>Your order ID is: <strong><?php echo $order_id; ?></strong></p>
    <p>We will process your order soon.</p>

    <h2>Order Details</h2>
    <div class="order-summary">
        <?php if ($order_items): ?>
            <?php foreach ($order_items as $item): ?>
                <div class="order-item">
                    <img src="../admin/uploads/<?php echo htmlspecialchars($item['product_image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['product_name']); ?>">
                    <div class="details">
                        <p><?php echo htmlspecialchars($item['product_name']); ?></p>
                        <p>Quantity: <?php echo htmlspecialchars($item['product_quantity']); ?></p>
                    </div>
                    <div class="price">
                        ₱<?php echo number_format($item['product_price'] * $item['product_quantity'], 2); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No items found for this order.</p>
        <?php endif; ?>
    </div>

    <a href="products.php">Continue Shopping</a>
</body>
</html>
