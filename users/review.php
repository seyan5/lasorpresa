<?php
// Include database configuration
include("conn.php");

// Fetch all reviews from the reviews table
try {
    $stmt = $pdo->prepare("SELECT r.review_id, r.review, r.rating, r.created_at, p.p_id, p.name AS product_name, p.featured_photo, c.cust_name 
                           FROM reviews r
                           JOIN product p ON r.product_id = p.p_id
                           JOIN customer c ON r.customer_id = c.cust_id
                           ORDER BY r.created_at DESC");
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching reviews: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"> <!-- Font Awesome for stars -->
    <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/review.css?">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/main.css?v=1.1">
</head>

<header>

        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>

        <!-- <a href="#" class="logo">Flower<span>.</span></a> -->
        <img src="../images/logo.png" alt="" class="logos" href="">
        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="#about">About</a>
            <div class="prod-dropdown">
                <a href="" onclick="toggleDropdown()">Products</a>
                <div class="prod-menu" id="prodDropdown">
                    <a href="products.php">Flowers</a>
                    <a href="occasion.php">Occasion</a>
                    <a href="addons.php">Addons</a>
                </div>
            </div>
            <a href="review.php">Review</a>
            <a href="#contacts">Contacts</a>
            <a href="customization.php">Customize</a>

        </nav>
        <div class="icons">
            <a href="shopcart.php" class="fas fa-shopping-cart"></a>
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
                            $paymentStatus = ($payment['payment_status'] == 'pending') ? 'Payment Pending' : ($payment['payment_status'] == 'paid' ? 'Payment Confirmed' : 'Payment Failed');
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

<body>

    <h1>Customer Reviews</h1>
<div class="container">
    <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="box">
                <div class="user">
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($review['cust_name']); ?></h3>
                        <span>Reviewed Product: <?php echo htmlspecialchars($review['product_name']); ?></span>
                    </div>
                </div>
                

                <!-- Review Text -->
                <h>comment: </h>
                <p><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                
                <!-- Product Image -->
                <img src="../admin/uploads/<?php echo htmlspecialchars($review['featured_photo']); ?>" alt="<?php echo htmlspecialchars($review['product_name']); ?>">

                <!-- Displaying Rating Stars -->
                <div class="stars">
                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                    <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                        <i class="far fa-star"></i>
                    <?php endfor; ?>
                </div>

                <!-- Review Date -->
                <div class="review-date">
                    <strong>Reviewed on:</strong> <?php echo date("F j, Y, g:i a", strtotime($review['created_at'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No reviews available.</p>
    <?php endif; ?>
</div>
<?php include('../loading.php'); ?>

</body>
</html>
