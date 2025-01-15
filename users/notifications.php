<?php
include("conn.php");

// Check if a customer is logged in
if (isset($_SESSION['customer']) && isset($_SESSION['customer']['cust_id'])) {
    $customerId = $_SESSION['customer']['cust_id']; // Get the logged-in customer's ID

    // Fetch payments for the logged-in customer with the necessary conditions
    $statement = $pdo->prepare("
        SELECT p.*, oi.product_id, pr.name
        FROM payment p
        JOIN order_items oi ON p.order_id = oi.order_id
        JOIN product pr ON oi.product_id = pr.p_id
        WHERE p.cust_id = :cust_id
        ORDER BY p.created_at DESC
    ");
    $statement->execute(['cust_id' => $customerId]);
    $payments = $statement->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Redirect if no customer is logged in
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>All Notifications</h2>

        <?php if (!empty($payments)): ?>
            <ul class="notifications-list">
                <?php foreach ($payments as $payment): ?>
                    <?php 
                    // Determine payment status message and shipping status message
                    $paymentStatus = ($payment['payment_status'] == 'pending') ? 'Payment Pending' : ($payment['payment_status'] == 'paid' ? 'Payment Confirmed' : 'Payment Failed');
                    $shippingStatus = ($payment['shipping_status'] == 'pending') ? 'Shipping Pending' : ($payment['shipping_status'] == 'shipped' ? 'Shipped' : 'Delivered');
                    ?>
                    <li class="notification-item">
                        <i class="fa fa-credit-card <?php echo $payment['payment_status'] == 'pending' ? 'bg-warning' : 'bg-success'; ?>"></i>
                        <div>
                            <a href="order-details.php?order_id=<?php echo $payment['order_id']; ?>&product_id=<?php echo $payment['product_id']; ?>" style="text-decoration: none;">
                                <strong>Product: <?php echo htmlspecialchars($payment['name']); ?></strong>
                                <div class="text-muted small"><?php echo $paymentStatus; ?></div>
                                <div class="text-muted small"><?php echo $shippingStatus; ?></div>
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No notifications available.</p>
        <?php endif; ?>
    </div>
</body>
</html>
