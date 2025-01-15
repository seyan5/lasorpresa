
<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");

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
                    <a href="orders/order.php">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
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
                            <div class="numbers">P<?= number_format($totalSalesAmount, 2) ?></div>
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

                <div class="iconBx">
                    <ion-icon name="cash-outline"></ion-icon>
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

           

            <!-- ================ Order Details List ================= -->
            <div class="recentOrders">
    <div class="cardHeader d-flex justify-content-between align-items-center">
        <h2>Recent Orders</h2>
        <a href="orders/order.php" class="btn btn-primary">View All</a>
    </div>

    <?php if (empty($recentOrders)) { ?>
        <p class="text-center">No orders found.</p>
    <?php } else { ?>
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
                        <td>$<?= number_format($order['current_price'], 2) ?></td>
                        <td><?= ucfirst(htmlspecialchars($order['payment_status'])) ?></td>
                        <td>
                            <span class="status 
                                <?= $order['shipping_status'] === 'delivered' ? 'delivered' : 
                                   ($order['shipping_status'] === 'pending' ? 'pending' : 
                                   ($order['shipping_status'] === 'in_progress' ? 'inProgress' : 'return')) ?>">
                                <?= ucfirst(htmlspecialchars($order['shipping_status'])) ?>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>

                <!-- ================= New Customers ================ -->
                <div class="recentCustomers">
                    <div class="cardHeader">
                        <h2>Recent Customer</h2>
                    </div>

                    <table>
                    <?php foreach ($recentCustomers as $customer) { ?>
                        <tr>
                            <td width="60px">
                                <div class="imgBx">
                                    
                                </div>
                            </td>
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
    </div>

    



    <!-- =========== Scripts =========  -->
    <script src="assets/js/main.js"></script>

    <!-- ====== ionicons ======= -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

</body>

<style>
    .recentOrders {
        margin-top: 20px;
    }

    .cardHeader {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .btn {
        font-size: 14px;
        padding: 8px 16px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th, table td {
        padding: 10px;
        text-align: left;
    }

    table th {
        background-color: #f8f9fa;
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
</style>
</html>