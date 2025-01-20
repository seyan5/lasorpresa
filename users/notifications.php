<?php
include("conn.php");

// Check if a customer is logged in
if (isset($_SESSION['customer']) && isset($_SESSION['customer']['cust_id'])) {
    $customerId = $_SESSION['customer']['cust_id'];

    // Fetch the notifications
    $statement = $pdo->prepare("
        SELECT p.*, oi.product_id, pr.name
        FROM payment p
        JOIN order_items oi ON p.order_id = oi.order_id
        JOIN product pr ON oi.product_id = pr.p_id
        WHERE p.cust_id = :cust_id
        AND (p.payment_status = 'pending' OR p.shipping_status != 'delivered')
        ORDER BY p.created_at DESC
    ");
    $statement->execute(['cust_id' => $customerId]);
    $payments = $statement->fetchAll(PDO::FETCH_ASSOC);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        h2 {
            background-color: #e18aaa;
            color: #fff;
            margin: 0;
            padding: 15px;
            text-align: center;
        }

        .notifications-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .notification-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.3s ease;
        }

        .notification-item:hover {
            background-color: #fdf0f5;
        }

        .notification-item i {
            font-size: 24px;
            margin-right: 15px;
        }

        .notification-item .bg-warning {
            color: #f39c12;
        }

        .notification-item .bg-success {
            color: #2ecc71;
        }

        .notification-item div {
            flex: 1;
        }

        .notification-item a {
            text-decoration: none;
            color: #333;
        }

        .notification-item a:hover {
            text-decoration: underline;
        }

        .text-muted {
            color: #7d7d7d;
        }

        .text-muted.small {
            font-size: 14px;
        }

        p {
            text-align: center;
            padding: 20px;
            color: #7d7d7d;
        }
    </style>
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
                            <a href="order-details.php?order_id=<?php echo $payment['order_id']; ?>&product_id=<?php echo $payment['product_id']; ?>">
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
