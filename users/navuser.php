<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lasorpresa</title>
    <!-- font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- css -->
     <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/dropdown.css?">
    <link rel="stylesheet" href="../css/main.css?v=1.1">
</head>

<>
    <!-- header -->

    <header>

        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>

        <!-- <a href="#" class="logo">Flower<span>.</span></a> -->
        <a href="index.php">
    <img src="../images/logo.png" alt="" class="logos">
</a>
        <nav class="navbar">
            <a href="products.php">Flowers</a>
            <a href="occasion.php">Occasion</a>
            <a href="addons.php">Addons</a>
            <a href="customization.php">Customize</a>
            <a href="review.php">Reviews</a>

        </nav>
        <div class="icons">
        <?php 
        $cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
        $count = count($cart);
        ?>
        <div class="cart-container">
    <a href="#" class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count"><?php echo $count; ?></span>
    </a>
    <div class="cart-dropdown">
        <p class="recent-products-title">Recently Added Products</p>
        <ul class="recent-products-list">
            <?php 
            if (!empty($cart)) {
                $totalProducts = 0;
                foreach ($cart as $item) {
                    $totalProducts++;
                    if ($totalProducts <= 5) { // Display only the first 5 items
                        echo '<li class="product-item">';
                        echo '<img src="../admin/uploads/' . (!empty($item['image']) ? htmlspecialchars($item['image']) : 'default-image.jpg') . '" alt="' . htmlspecialchars($item['name']) . '" class="product-image" style="width:50px; height:50px; object-fit:cover; margin-right:10px;">';
                        echo '<span class="product-name">' . htmlspecialchars($item['name']) . '</span>';
                        echo '<span class="product-price">₱' . htmlspecialchars($item['price']) . '</span>';
                        echo '</li>';
                    }
                }
            } else {
                echo '<li class="empty-cart">Your cart is empty</li>';
            }
            ?>
        </ul>
        <?php if (count($cart) > 5): ?>
        <a href="shopcart.php" class="view-cart-link">View <?php echo count($cart) - 5; ?> More Products In Cart</a>
        <?php endif; ?>
        <a href="shopcart.php" class="view-shopping-cart-button">View My Shopping Cart</a>
    </div>
</div>

            <div class="user-dropdown">
                <a href="#" class="fas fa-user" onclick="toggleDropdown()"></a>
                <div class="dropdown-menu" id="userDropdown">
                    <?php if (isset($_SESSION['customer'])): ?>
                        <p>Welcome, <?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?></p>
                        <hr>
                        <a href="customer-profile-update.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>


            <div class="notification-dropdown">
                <a href="#" class="fas fa-bell" onclick="toggleNotificationDropdown()"></a>
                <div class="dropdown-menu" id="notificationDropdown">
                    <?php 
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
                            AND (p.payment_status = 'pending' OR p.shipping_status != 'delivered') 
                            ORDER BY p.created_at DESC
                        ");
                        $statement->execute(['cust_id' => $customerId]);
                        $payments = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($payments)): 
                    ?>
                        <p>Notifications</p>
                        <hr>
                        <?php foreach ($payments as $payment): ?>
                            <?php 
                            // Determine payment status message and shipping status message
                            $paymentStatus = ($payment['payment_status'] == 'pending') ? 'Payment Pending' : ($payment['payment_status'] == 'paid' ? 'Paid' : 'Payment Failed');
                            $shippingStatus = ($payment['shipping_status'] == 'pending') ? 'Shipping Pending' : ($payment['shipping_status'] == 'shipped' ? 'Shipped' : 'Delivered');
                            ?>
                            <li class="dropdown-item d-flex align-items-center">
                                <i class="fa fa-credit-card me-2 <?php echo $payment['payment_status'] == 'pending' ? 'bg-warning' : 'bg-success'; ?>" style="padding: 5px; border-radius: 50%;"></i>
                                <div>
                                    <a href="order-details.php?order_id=<?php echo $payment['order_id']; ?>&product_id=<?php echo $payment['product_id']; ?>" style="text-decoration: none;">
                                        <strong>Product: <?php echo $payment['name']; ?></strong>
                                        <div class="text-muted small"><?php echo $paymentStatus; ?></div>
                                        <div class="text-muted small"><?php echo $shippingStatus; ?></div>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <hr>
                        <a href="notifications.php" class="btn btn-link">View All</a>
                    <?php else: ?>
                        <li>
                            <span class="dropdown-item text-center text-muted">No new notifications</span>
                        </li>
                    <?php endif; ?>
                    <?php 
                    } else { 
                    ?>
                        <li>
                            <span class="dropdown-item text-center text-muted">No customer logged in</span>
                        </li>
                    <?php } ?>
                </div>
            </div>
        </div>
        </div>
    </div>
</header>


<script>
    // Function to toggle the notification dropdown
function toggleCartDropdown(event) {
    event.preventDefault(); // Prevent default link behavior
    const dropdown = document.getElementById('notificationDropdown');
    
    // Toggle dropdown visibility
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    } else {
        dropdown.style.display = 'block';
    }
}

// Close the dropdown when clicking outside
document.addEventListener('click', (event) => {
    const dropdown = document.getElementById('notificationDropdown');
    const bellIcon = document.querySelector('.fas.fa-bell');

    // Check if the click is outside the dropdown and the bell icon
    if (dropdown.style.display === 'block' && !dropdown.contains(event.target) && event.target !== bellIcon) {
        dropdown.style.display = 'none';
    }
});

</script>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Close the dropdown when clicking outside
    window.onclick = function (event) {
        if (!event.target.matches('.fa-user')) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    };
</script>

<script>
    // Toggle the user dropdown when clicking the user icon
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Toggle the notifications dropdown when clicking the bell icon
    function toggleNotificationDropdown() {
        const notificationDropdown = document.getElementById('notificationDropdown');
        notificationDropdown.classList.toggle('show');
    }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function (event) {
        // Close user dropdown if clicked outside
        if (!event.target.matches('.fa-user')) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }

        // Close notifications dropdown if clicked outside
        if (!event.target.matches('.fa-bell')) {
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown && notificationDropdown.classList.contains('show')) {
                notificationDropdown.classList.remove('show');
            }
        }
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if ("Notification" in window) {
            // Request notification permission if not granted
            Notification.requestPermission().then(permission => {
                if (permission !== "granted") {
                    console.log("Notification permission denied.");
                }
            });

            // Check payment and shipping statuses and schedule notifications
            var paymentUpdates = [];
            
            <?php foreach ($payments as $payment): ?>
                var paymentStatus = '<?php echo $payment['payment_status']; ?>';
                var shippingStatus = '<?php echo $payment['shipping_status']; ?>';
                var paymentId = '<?php echo $payment['name']; ?>';

                if (paymentStatus === 'pending' || shippingStatus !== 'delivered') {
                    paymentUpdates.push({
                        id: paymentId,
                        paymentStatus: paymentStatus,
                        shippingStatus: shippingStatus
                    });
                }
            <?php endforeach; ?>

            // Function to show notification
            function showPaymentShippingNotification(payment) {
                const options = {
                    body: `Payment Status: ${payment.paymentStatus}, Shipping Status: ${payment.shippingStatus}`,
                    icon: '../images/logo.png',
                    tag: `payment-shipping-${payment.id}`
                };
                new Notification(`Product ${payment.id}`, options);
            }

            // Display notifications at intervals (2 seconds)
            if (paymentUpdates.length > 0 && Notification.permission === "granted") {
                let index = 0;
                const notificationInterval = setInterval(() => {
                    if (index < paymentUpdates.length) {
                        showPaymentShippingNotification(paymentUpdates[index]);
                        index++;
                    } else {
                        clearInterval(notificationInterval); // Stop interval after all notifications
                    }
                }, 2000); // 2 seconds interval
            }
        }
    });
</script>
