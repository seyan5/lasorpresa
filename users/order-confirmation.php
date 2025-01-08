<?php
require 'header.php';

// Check if order ID is passed in the URL
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    echo "Invalid order.";
    exit;
}

$order_id = $_GET['order_id'];

// Fetch the order details from the database
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id");
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// If the order doesn't exist, show an error message
if (!$order) {
    echo "Order not found.";
    exit;
}

// Fetch the order items
$stmt = $pdo->prepare("SELECT oi.*, p.name, p.featured_photo FROM order_items oi
                        JOIN product p ON oi.p_id = p.p_id
                        WHERE oi.order_id = :order_id");
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
$stmt->execute();
$order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customer details using the customer ID from the order
$stmt = $pdo->prepare("SELECT * FROM customer WHERE cust_id = :cust_id");
$stmt->bindParam(':cust_id', $order['cust_id'], PDO::PARAM_INT);
$stmt->execute();
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
    <div class="container">
        <h1>Order Confirmation</h1>
        
        <div class="order-summary">
            <h3>Your Order has been Placed Successfully!</h3>
            <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
            <p><strong>Order Date:</strong> <?php echo date('F d, Y', strtotime($order['order_date'])); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
            <p><strong>Total Amount:</strong> ₱<?php echo number_format($order['total_price'], 2); ?></p>
            
            <!-- Fetch and display customer shipping address -->
            <p><strong>Shipping Address:</strong></p>
            <ul>
                <li><?php echo htmlspecialchars($customer['cust_s_address'] ?? 'N/A'); ?></li>
                <li><?php echo htmlspecialchars($customer['cust_s_city'] ?? 'N/A'); ?></li>
                <li>Zip: <?php echo htmlspecialchars($customer['cust_s_zip'] ?? 'N/A'); ?></li>
                <li>Phone: <?php echo htmlspecialchars($customer['cust_s_phone'] ?? 'N/A'); ?></li>
            </ul>
        </div>

        <h3>Order Items</h3>
        <div class="order-items">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td>
                            <?php if ($item['featured_photo']): ?>
                                <img src="../admin/uploads/<?php echo htmlspecialchars($item['featured_photo']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
                            <?php else: ?>
                                <img src="../images/default-image.jpg" alt="No image available" width="50">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>₱<?php echo number_format($item['total_price'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="thank-you-message">
            <h3>Thank you for shopping with us!</h3>
            <p>We will process your order and notify you once it's shipped. If you have any questions, feel free to contact our support team.</p>
        </div>

        <div class="action-links">
            <a href="index.php" class="btn">Return to Home Page</a>
            <a href="order_history.php" class="btn">View Order History</a>
        </div>
    </div>
</body>
</html>

<style>
    .container {
        width: 80%;
        margin: auto;
        padding: 20px;
    }
    .order-summary {
        background-color: #f4f4f4;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
    }
    .order-summary h3 {
        margin-bottom: 20px;
    }
    .order-summary p {
        font-size: 16px;
    }
    .order-items table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    .order-items th, .order-items td {
        padding: 10px;
        border: 1px solid #ddd;
        text-align: center;
    }
    .order-items th {
        background-color: #f4f4f4;
    }
    .thank-you-message {
        margin-top: 30px;
        font-size: 18px;
        text-align: center;
    }
    .action-links {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }
    .action-links .btn {
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        text-align: center;
    }
    .action-links .btn:hover {
        background-color: #0056b3;
    }
</style>
</body>
</html>
