<?php
require_once('header.php'); // Include DB connection and session start

if (!isset($_GET['order_id'])) {
    echo "No order ID provided.";
    exit;
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$stmt = $pdo->prepare("SELECT * FROM custom_order WHERE order_id = :order_id");
$stmt->execute(['order_id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "Order not found.";
    exit;
}

// Fetch order items
$stmt = $pdo->prepare("
    SELECT coi.*, 
           f.name AS flower_name, 
           c.container_name, 
           col.color_name
    FROM custom_orderitems coi
    LEFT JOIN flowers f ON coi.flower_type = f.id
    LEFT JOIN container c ON coi.container_type = c.container_id
    LEFT JOIN color col ON coi.container_color = col.color_id
    WHERE coi.order_id = :order_id
");
$stmt->execute(['order_id' => $order_id]);
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS -->
</head>
<body>
    <div class="order-confirmation">
        <h1>Thank You for Your Order!</h1>
        <p>Your order ID is: <strong><?php echo htmlspecialchars($order['order_id']); ?></strong></p>
        <p>We will process your order soon.</p>
        
        <h3>Order Details</h3>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
        <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'], 2); ?></p>

        <h3>Items in Your Order</h3>
        <?php if ($order_items): ?>
            <table>
                <thead>
                    <tr>
                        <th>Flower Type</th>
                        <th>Number of Flowers</th>
                        <th>Container Type</th>
                        <th>Container Color</th>
                        <th>Item Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['flower_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['num_flowers']); ?></td>
                            <td><?php echo htmlspecialchars($item['container_name']); ?></td>
                            <td><?php echo htmlspecialchars($item['color_name']); ?></td>
                            <td>$<?php echo number_format($item['total_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No items found for this order.</p>
        <?php endif; ?>

        <a href="index.php" class="btn">Continue Shopping</a>
    </div>
</body>
</html>
