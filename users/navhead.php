<div class="icons">
    <?php 
    // Initialize notifications count
    $notificationCount = 0;

    if (isset($_SESSION['customer'])) {
        $cust_id = $_SESSION['customer']['cust_id'];

        // Fetch the count of notifications for the logged-in user
        $stmt = $pdo->prepare("SELECT COUNT(*) AS notification_count FROM payment WHERE cust_id = :cust_id");
        $stmt->execute(['cust_id' => $cust_id]);
        $notificationData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Set the count based on the fetched data
        $notificationCount = $notificationData['notification_count'] ?? 0;
    }
    ?>
    <div class="cart-container">
        <!-- Notification Icon with Count -->
        <a href="notifications.php" class="notification-icon">
            <i class="fas fa-bell"></i>
            <span class="notification-count"><?php echo $notificationCount; ?></span>
        </a>
        <div class="notification-dropdown">
            <p class="recent-notifications-title">Recent Notifications</p>
            <ul class="recent-notifications-list">
                <?php 
                // Fetch notifications for the logged-in user
                $notifications = [];
                if (isset($_SESSION['customer'])) {
                    $stmt = $pdo->prepare("
                        SELECT p.*, oi.product_id, pr.name, p.payment_status, p.shipping_status
                        FROM payment p
                        JOIN order_items oi ON p.order_id = oi.order_id
                        JOIN product pr ON oi.product_id = pr.p_id
                        WHERE p.cust_id = :cust_id
                        ORDER BY p.created_at DESC
                    ");
                    $stmt->execute(['cust_id' => $cust_id]);
                    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                // Display notifications
                if (!empty($notifications)) {
                    $totalNotifications = 0;
                    foreach ($notifications as $notification) {
                        $totalNotifications++;
                        if ($totalNotifications <= 5) { // Display only the first 5 notifications
                            $paymentStatus = ($notification['payment_status'] == 'pending') ? 'Payment Pending' : ($notification['payment_status'] == 'paid' ? 'Paid' : 'Payment Failed');
                            $shippingStatus = ($notification['shipping_status'] == 'pending') ? 'Shipping Pending' : ($notification['shipping_status'] == 'shipped' ? 'Shipped' : 'Delivered');
                            echo '<li class="notification-item">';
                            echo '<span class="notification-product">' . htmlspecialchars($notification['name']) . '</span>';
                            echo '<span class="notification-status">Status: ' . $paymentStatus . '</span>';
                            echo '<span class="notification-shipping-status">Shipping: ' . $shippingStatus . '</span>';
                            echo '</li>';
                        }
                    }
                } else {
                    echo '<li class="empty-notifications">No new notifications</li>';
                }
                ?>
            </ul>
            <?php if (!empty($notifications) && count($notifications) > 5): ?>
                <a href="notifications.php" class="view-notifications-link" style="font-size: 1.3rem;">
                    View <?php echo count($notifications) - 5; ?> More Notifications
                </a>
            <?php endif; ?>
            <a href="notifications.php" class="view-notifications-button">View All Notifications</a>
        </div>
    </div>
</div>
