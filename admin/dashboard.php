<?php
ob_start();
include_once('conn.php');
include('auth.php');

// Query to fetch recent customers, ordered by the registration date
try {
    $recentCustomersQuery = $pdo->query("SELECT * FROM customer ORDER BY cust_datetime DESC LIMIT 8");
    $recentCustomers = $recentCustomersQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching recent customers: " . $e->getMessage();
}

$totalUsersQuery = $pdo->query("SELECT COUNT(cust_id) AS total_users FROM customer");
$totalUsers = $totalUsersQuery->fetch(PDO::FETCH_ASSOC)['total_users'];

$totalSalesQuery = $pdo->query("SELECT COUNT(order_id) AS total_sales FROM orders");
$totalSales = $totalSalesQuery->fetch(PDO::FETCH_ASSOC)['total_sales'];

$listedProductsQuery = $pdo->query("SELECT COUNT(p_id) AS listed_products FROM product");
$listedProducts = $listedProductsQuery->fetch(PDO::FETCH_ASSOC)['listed_products'];

$totalSalesAmountQuery = $pdo->query("SELECT SUM(amount_paid) AS total_sales_amount FROM payment");
$totalSalesAmount = $totalSalesAmountQuery->fetch(PDO::FETCH_ASSOC)['total_sales_amount'];

// Provide a default value of 0 if the total sales amount is null
$totalSalesAmount = $totalSalesAmount ? $totalSalesAmount : 0;

$paymentPendingQuery = $pdo->query("SELECT COUNT(order_id) AS payment_pending FROM payment WHERE payment_status = 'pending'");
$paymentPending = $paymentPendingQuery->fetch(PDO::FETCH_ASSOC)['payment_pending'];

$shippingPendingQuery = $pdo->query("SELECT COUNT(order_id) AS shipping_pending FROM payment WHERE shipping_status = 'pending'");
$shippingPending = $shippingPendingQuery->fetch(PDO::FETCH_ASSOC)['shipping_pending'];

try {
    // Fetch recent orders from the payment table
    $recentOrdersQuery = $pdo->query("
        SELECT 
            c.cust_name, 
            p.name AS product_name, 
            p.current_price, 
            pay.payment_status, 
            pay.shipping_status, 
            pay.created_at AS payment_date
        FROM payment pay
        JOIN orders o ON pay.order_id = o.order_id
        JOIN customer c ON o.customer_id = c.cust_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.p_id
        ORDER BY pay.created_at DESC 
        LIMIT 8
    ");
    $recentOrders = $recentOrdersQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "Error fetching recent orders: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Sorpresa Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>
<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <div class="logo-container">
                            <img src="../images/logo.png" alt="Logo" class="logo" />
                        </div>
                        <span class="title"></span>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Users</span>
                    </a>
                </li>
                <li>
                    <a href="sales-report.php">
                        <span class="icon">
                            <ion-icon name="cash-outline"></ion-icon>
                        </span>
                        <span class="title">Sales</span>
                    </a>
                </li>
                <li>
                    <a href="product/product.php">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>
                <li>
                    <a href="product/flowers.php">
                        <span class="icon">
                            <ion-icon name="flower-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Flowers</span>
                    </a>
                </li>
                <li>
                    <a href="orders/order.php">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
                    </a>
                </li>
                <li>
                    <a href="customize/customize-order.php">
                        <span class="icon">
                        <ion-icon name="color-wand-outline"></ion-icon>
                        </span>
                        <span class="title"> Customize Orders</span>
                    </a>
                </li>
                <li>
                    <a href="wishlist.php">
                        <span class="icon">
                        <ion-icon name="heart-outline"></ion-icon>
                        </span>
                        <span class="title"> Wishlists</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <span class="icon">
                            <ion-icon name="albums-outline"></ion-icon>
                        </span>
                        <span class="title">Categories</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <div class="search">
                    <label>
                        <input type="text" placeholder="Search here">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>

                <div class="user">
                    <img src="assets/imgs/customer01.jpg" alt="">
                </div>
            </div>

            <!-- ======================= Cards ================== -->
            <div class="cardBox">
                <div class="card">
                    <div>
                        <div class="numbers"><?= number_format($totalUsers) ?></div>
                        <div class="cardName">Total Users</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="eye-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <div>
                        <div class="numbers"><?= number_format($totalSales) ?></div>
                        <div class="cardName">Sales</div>
                    </div>

                    <div class="iconBx">
                        <ion-icon name="cart-outline"></ion-icon>
                    </div>
                </div>

                <div class="card">
                    <a href="product/product.php" style="text-decoration: none; color: inherit;">
                        <div>
                            <div class="numbers"><?= number_format($listedProducts) ?></div>
                            <div class="cardName">Listed Products</div>
                        </div>

                        <div class="iconBx">
                            <ion-icon name="cube-outline"></ion-icon>
                        </div>
                    </a>
                </div>

                <div class="card">
                    <a href="sales-report.php" style="text-decoration: none; color: inherit;">
                        <div>
                            <div class="numbers">P <?= number_format($totalSalesAmount, 2) ?></div>
                            <div class="cardName">Total Sales</div>
                        </div>
                        <div class="iconBx">
                            <ion-icon name="cash-outline"></ion-icon>
                        </div>
                    </a>
                </div>

                <div class="card">
                <div>
                    <div class="numbers"><?= number_format($paymentPending) ?></div>
                    <div class="cardName">Payment Pending</div>
                </div>
            </div>

            <div class="card">
                <div>
                    <div class="numbers"><?= number_format($shippingPending) ?></div>
                    <div class="cardName">Shipping Pending</div>
                </div>

                <div class="iconBx">
                    <ion-icon name="cart-outline"></ion-icon>
                </div>
            </div>
        </div>

        <!-- Recent Orders and Recent Customers Side by Side -->
        <div class="row">
            <div class="recentOrders">
                <div class="cardHeader d-flex justify-content-between align-items-center">
                    <h2>Recent Orders</h2>
                    <a href="orders/order.php" class="btn btn-primary">View All</a>
                </div>

                <!-- Order table content goes here -->
                <table class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Payment Status</th>
                            <th>Shipping Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order) { ?>
                            <tr>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td>₱ <?= number_format($order['current_price'], 2) ?></td>
                                <td><?= ucfirst(htmlspecialchars($order['payment_status'])) ?></td>
                                <td>
                                    <span class="status 
                                        <?php 
                                            switch ($order['shipping_status']) {
                                                case 'delivered':
                                                    echo 'delivered';
                                                    break;
                                                case 'pending':
                                                    echo 'pending';
                                                    break;
                                                case 'in_progress':
                                                    echo 'inProgress';
                                                    break;
                                                default:
                                                    echo 'return';
                                                    break;
                                            }
                                        ?>">
                                        <?= ucfirst(htmlspecialchars($order['shipping_status'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Recent Customers next to recent orders -->
            <div class="recentCustomers">
                <div class="cardHeader">
                    <h2>Recent Customers</h2>
                    <a href="users.php" class="btn btn-primary">View All</a>
                </div>

                <!-- Customer table content goes here -->
                <table>
                    <?php foreach ($recentCustomers as $customer) { ?>
                        <tr>
                            <!-- <td width="60px">
                                <div class="imgBx"></div>
                            </td> -->
                            <td>
                                <h4><?= htmlspecialchars($customer['cust_name']) ?><br>
                                    <span><?= htmlspecialchars($customer['cust_city']) ?></span>
                                </h4>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</body>
<style>
    .recentOrders {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 70%; /* Don't override .container styles */
}

.recentCustomers {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    width: 30%; /* Don't override .container styles */
}

.row {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-top: 20px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 10px;
    text-align: left;
}

.table th {
    background-color: #f4f4f4;
}

.status {
    padding: 5px 10px;
    border-radius: 3px;
    font-weight: bold;
}

.status.delivered {
    background-color: #28a745;
    color: white;
}

.status.pending {
    background-color: #ffc107;
    color: white;
}

.status.inProgress {
    background-color: #007bff;
    color: white;
}

.status.return {
    background-color: #dc3545;
    color: white;
}
.cardHeader {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px; /* Optional: Adds space below the header */
}

.cardHeader h2 {
    margin: 0; /* Remove default margin to align it properly */
}

.cardHeader .btn {
    display: inline-block;
    background-color: #007bff;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 14px;
}

.cardHeader .btn:hover {
    background-color: #0056b3;
}


</style>
</html>
