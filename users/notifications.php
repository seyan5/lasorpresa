<?php
include("conn.php");

// Check if a customer is logged in
if (isset($_SESSION['customer']) && isset($_SESSION['customer']['cust_id'])) {
    $customerId = $_SESSION['customer']['cust_id'];

    // Fetch all notifications, including their viewed status
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
}
?>

<?php include('navuser.php'); ?>

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

        .container1 {
            max-width: 800px;
            margin: 100px auto;
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

        .notifications-list1 {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .notification-item1 {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.3s ease;
        }

        .notification-item1:hover {
            background-color: #fdf0f5;
        }

        .notification-item1 i {
            font-size: 24px;
            margin-right: 15px;
        }

        .notification-item1 .bg-warning {
            color: #f39c12;
        }

        .notification-item1 .bg-success {
            color: #2ecc71;
        }

        .notification-item1 .bg-delivered {
            color: #3498db;
        }

        .notification-item1 .bg-ready {
            color: #f39c12; /* Optionally, use a different color for 'Ready for Pickup' */
        }

        .notification-item1 div {
            flex: 1;
        }

        .notification-item1 a {
            text-decoration: none;
            color: #333;
        }

        .notification-item1 a:hover {
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

        /* Add a background for delivered notifications */
        .delivered-item {
            background-color: #eaf5ff;
        }
    </style>
</head>
<body>
    <div class="container1">
        <h2>All Notifications</h2>

        <?php if (!empty($payments)): ?>
    <ul class="notifications-list1">
        <?php foreach ($payments as $payment): ?>
            <?php 
            // Determine statuses
            $paymentStatus = ($payment['payment_status'] == 'pending') ? 'Payment Pending' : 
                             ($payment['payment_status'] == 'paid' ? 'Payment Confirmed' : 'Payment Failed');

            // Modified Shipping status logic to include 'Ready for Pickup'
            $shippingStatus = ($payment['shipping_status'] == 'pending') ? 'Shipping Pending' : 
                              ($payment['shipping_status'] == 'shipped' ? 'Shipped' : 
                              ($payment['shipping_status'] == 'delivered' ? 'Delivered' : 
                              ($payment['shipping_status'] == 'readyforpickup' ? 'Ready for Pickup' : '')));

            $isDelivered = $payment['shipping_status'] == 'delivered';
            $isReadyForPickup = $payment['shipping_status'] == 'readyforpickup'; // New check for "Ready for Pickup"
            $isViewed = $payment['viewed'] == 1; // Check if notification is viewed
            ?>
            <li class="notification-item1 <?php echo $isViewed ? 'viewed-item' : 'not-viewed-item'; ?> <?php echo $isDelivered ? 'delivered-item' : ''; ?>">
                <i class="fa fa-credit-card <?php echo $isDelivered ? 'bg-delivered' : ($isReadyForPickup ? 'bg-ready' : ($payment['payment_status'] == 'pending' ? 'bg-warning' : 'bg-success')); ?>"></i>
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

    <style>
        /* Viewed notifications */
.viewed-item {
    background-color: #f0f0f0; /* Light gray background for viewed notifications */
    color: #999; /* Lighter text color for viewed */
}

/* Not viewed notifications */
.not-viewed-item {
    background-color: #fff; /* Default white background */
    font-weight: bold; /* Bold text for emphasis */
}

/* Optional hover effects */
.not-viewed-item:hover {
    background-color: #fdf0f5; /* Slightly different color for hover */
}

    </style>
</body>
</html>
