<?php
require_once('conn.php');

// Check if the customer is logged in or not
if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
} else {
    // If customer is logged in, but admin makes them inactive, force logout
    $statement = $pdo->prepare("SELECT * FROM customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'], 0));
    $total = $statement->rowCount();
    if ($total) {
        header('location: ' . BASE_URL . 'logout.php');
        exit;
    }
}


if (isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';

    // Validate fields
    if (empty($_POST['cust_name'])) {
        $valid = 0;
        $error_message .= "Customer Name can't be Empty<br>";
    }

    if (empty($_POST['cust_phone'])) {
        $valid = 0;
        $error_message .= "Customer Phone can't be Empty<br>";
    }

    if (empty($_POST['cust_address'])) {
        $valid = 0;
        $error_message .= "Customer Address can't be Empty<br>";
    }

    if (empty($_POST['cust_city'])) {
        $valid = 0;
        $error_message .= "Customer City can't be Empty<br>";
    }

    if (empty($_POST['cust_zip'])) {
        $valid = 0;
        $error_message .= "Customer Zip can't be Empty<br>";
    }

    // If validation passes, update the profile
    if ($valid == 1) {
        $stmt = $pdo->prepare("UPDATE customer SET cust_name=?, cust_phone=?, cust_address=?, cust_city=?, cust_zip=? WHERE cust_id=?");
        $stmt->execute(array(
            strip_tags($_POST['cust_name']),
            strip_tags($_POST['cust_phone']),
            strip_tags($_POST['cust_address']),
            strip_tags($_POST['cust_city']),
            strip_tags($_POST['cust_zip']),
            $_SESSION['customer']['cust_id']
        ));

        // Fetch updated customer data and update session
        $statement = $pdo->prepare("SELECT * FROM customer WHERE cust_id = ?");
        $statement->execute([$_SESSION['customer']['cust_id']]);
        $updatedCustomer = $statement->fetch(PDO::FETCH_ASSOC);

        // Update session variables with updated customer data
        $_SESSION['customer']['cust_name'] = $updatedCustomer['cust_name'];
        $_SESSION['customer']['cust_phone'] = $updatedCustomer['cust_phone'];
        $_SESSION['customer']['cust_address'] = $updatedCustomer['cust_address'];
        $_SESSION['customer']['cust_city'] = $updatedCustomer['cust_city'];
        $_SESSION['customer']['cust_zip'] = $updatedCustomer['cust_zip'];

        $success_message = "Profile Information Updated successfully.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/navhead.css?">
    <link rel="stylesheet" href="../css/profileupd.css">
    <title>Navbar Fix</title>
</head>
<body>
<header>
    <img src="../images/logo.png" alt="Logo" class="logos">
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="customer-profile-update.php">Update Profile</a>
        <a href="customer-password-update.php">Update Password</a>
        <a href="customer-order.php">Orders</a>
        <a href="customize-view.php">Custom Orders</a>
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

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <h3><?php echo "Update Profile"; ?></h3>
                    <?php
                    if (!empty($error_message)) {
                        echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>" . $error_message . "</div>";
                    }
                    if (!empty($success_message)) {
                        echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>" . $success_message . "</div>";
                    }
                    ?>
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="">Customer Name *</label>
                                <input type="text" class="form-control" name="cust_name" value="<?php echo isset($_SESSION['customer']['cust_name']) ? htmlspecialchars($_SESSION['customer']['cust_name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Email Address *</label>
                                <input type="text" class="form-control" value="<?php echo isset($_SESSION['customer']['cust_email']) ? htmlspecialchars($_SESSION['customer']['cust_email']) : ''; ?>" disabled>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Phone Number *</label>
                                <input type="text" class="form-control" name="cust_phone" value="<?php echo isset($_SESSION['customer']['cust_phone']) ? htmlspecialchars($_SESSION['customer']['cust_phone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="">Address *</label>
                                <textarea name="cust_address" class="form-control" cols="30" rows="10" style="height:70px;" required><?php echo isset($_SESSION['customer']['cust_address']) ? htmlspecialchars($_SESSION['customer']['cust_address']) : ''; ?></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">City *</label>
                                <input type="text" class="form-control" name="cust_city" value="<?php echo isset($_SESSION['customer']['cust_city']) ? htmlspecialchars($_SESSION['customer']['cust_city']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Zip Code *</label>
                                <input type="text" class="form-control" name="cust_zip" value="<?php echo isset($_SESSION['customer']['cust_zip']) ? htmlspecialchars($_SESSION['customer']['cust_zip']) : ''; ?>" required>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Update" name="form1">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
