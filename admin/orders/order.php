<?php
ob_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");
require_once '../auth.php';

// Pagination Variables
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch total number of records for pagination
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_records = $stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Query to join the relevant tables with pagination
$stmt = $pdo->prepare("SELECT 
    c.cust_id, 
    c.cust_name, 
    c.cust_email, 
    p.name AS product_name, 
    oi.quantity, 
    p.current_price AS unit_price, 
    pay.payment_method, 
    pay.payment_id, 
    pay.created_at AS payment_date, 
    pay.amount_paid, 
    pay.shipping_status, 
    pay.payment_status, 
    o.order_id,
    o.order_status  -- Include order status
FROM 
    customer c
JOIN orders o ON c.cust_id = o.customer_id
JOIN order_items oi ON o.order_id = oi.order_id
JOIN product p ON oi.product_id = p.p_id
JOIN payment pay ON o.order_id = pay.order_id
ORDER BY pay.created_at DESC
LIMIT :limit OFFSET :offset");


$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle update and delete requests (same as the original code)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    try {
        $order_id = $_POST['order_id'];
        $status_column = $_POST['status_column']; // Either 'payment_status' or 'shipping_status'
        $new_status = $_POST['new_status'];

        // Validate inputs
        if (!in_array($status_column, ['payment_status', 'shipping_status'])) {
            throw new Exception("Invalid status column");
        }

        // Update the database
        $stmt = $pdo->prepare("UPDATE payment SET $status_column = :new_status WHERE order_id = :order_id");
        $stmt->execute([
            ':new_status' => $new_status,
            ':order_id' => $order_id
        ]);

        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Sorpresa Admin</title>
    <link rel="stylesheet" href="../../css/settings.css?v.1.1">
    <link rel="stylesheet" href="../../css/navdash.css">
    <link rel="stylesheet" href="../../css/products.css">
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
                            <img src="../../images/logo.png" alt="Logo" class="logo" />
                        </div>
                        <span class="title"></span>
                    </a>
                </li>
                <li>
                    <a href="../dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../users.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Users</span>
                    </a>
                </li>
                <li>
                    <a href="../sales-report.php">
                        <span class="icon">
                            <ion-icon name="cash-outline"></ion-icon>
                        </span>
                        <span class="title">Sales</span>
                    </a>
                </li>
                <li>
                    <a href="../product/product.php">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>
                <li>
                    <a href="../product/flowers.php">
                        <span class="icon">
                            <ion-icon name="flower-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Flowers</span>
                    </a>
                </li>
                <li>
                    <a href="order.php">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
                    </a>
                </li>
                <li>
                    <a href="../customize/customize-order.php">
                        <span class="icon">
                        <ion-icon name="color-wand-outline"></ion-icon>
                        </span>
                        <span class="title"> Customize Orders</span>
                    </a>
                </li>
                <li>
                    <a href="../settings.php">
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

        <div class="first-theme">
    <h1>Order Dashboard</h1>
    <div class="tbl-header">
        <table cellpadding="0" cellspacing="0" border="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Product Details</th>
                    <th>Payment Information</th>
                    <th>Paid Amount</th>
                    <th>Payment Status</th>
                    <th>Shipping Status</th>
                    <th>Order Status</th>
                    <th>Action</th>
                </tr>
            </thead>
                    <tbody>
                        <?php foreach ($orders as $index => $order) : ?>
                            <tr>
                                <td><?= $index + 1 + $offset ?></td>
                                <td>
                                    <strong>Id:</strong> <?= $order['cust_id'] ?><br>
                                    <strong>Name:</strong> <?= htmlspecialchars($order['cust_name']) ?><br>
                                    <strong>Email:</strong> <?= htmlspecialchars($order['cust_email']) ?>
                                </td>
                                <td>
                                    <strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?><br>
                                    <strong>Quantity:</strong> <?= $order['quantity'] ?><br>
                                    <strong>Unit Price:</strong> ₱<?= number_format($order['unit_price'], 2) ?>
                                </td>
                                <td>
                                    <strong>Payment Method:</strong> <?= $order['payment_method'] ?><br>
                                    <strong>Payment Id:</strong> <?= $order['payment_id'] ?><br>
                                    <strong>Date:</strong> <?= $order['payment_date'] ?>
                                </td>
                                <td>₱<?= number_format($order['amount_paid'], 2) ?></td>
                                <td>
                                    <select class="form-select" id="payment-status-<?= $order['order_id'] ?>">
                                        <option <?= $order['payment_status'] === 'pending' ? 'selected' : '' ?> value="pending">Pending</option>
                                        <option <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?> value="paid">Paid</option>
                                        <option <?= $order['payment_status'] === 'failed' ? 'selected' : '' ?> value="failed">Failed</option>
                                    </select>
                                </td>
                                <td>
                                    <select class="form-select" id="shipping-status-<?= $order['order_id'] ?>">
                                        <option <?= $order['shipping_status'] === 'pending' ? 'selected' : '' ?> value="pending">Pending</option>
                                        <option <?= $order['shipping_status'] === 'shipped' ? 'selected' : '' ?> value="shipped">Shipped</option>
                                        <option <?= $order['shipping_status'] === 'delivered' ? 'selected' : '' ?> value="delivered">Delivered</option>
                                    </select>
                                </td>
                                <td><?= htmlspecialchars($order['order_status']) ?></td>
                                <td>
                                <button class="btn btn-primary" title="Update Order" onclick="updateOrderStatus(<?= $order['order_id'] ?>)">
                                    <ion-icon name="pencil-outline"></ion-icon>
                                </button>
                                <button class="btn btn-danger" title="Delete Order" onclick="deleteOrder(<?= $order['order_id'] ?>)">
                                    <ion-icon name="trash-outline"></ion-icon>
                                </button>
                                    <!-- <button class="btn btn-primary" onclick="updateOrderStatus(<?= $order['order_id'] ?>)">Update</button>
                                    <button class="btn btn-danger" onclick="deleteOrder(<?= $order['order_id'] ?>)">Delete</button> -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

        </table>
    </div>
    <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="?page=<?= $i ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>


    <script>
        function updateOrderStatus(orderId) {
            const paymentStatus = document.getElementById(`payment-status-${orderId}`).value;
            const shippingStatus = document.getElementById(`shipping-status-${orderId}`).value;

            // Update Payment Status
            fetch('order-change-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    order_id: orderId,
                    status_column: 'payment_status',
                    new_status: paymentStatus,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch((error) => console.error('Error:', error));

            // Update Shipping Status
            fetch('order-change-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    order_id: orderId,
                    status_column: 'shipping_status',
                    new_status: shippingStatus,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch((error) => console.error('Error:', error));
        }

        function deleteOrder(orderId) {
            if (confirm("Are you sure you want to delete this order?")) {
                fetch("order-delete.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams({
                        order_id: orderId,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                            // Remove the row from the table
                            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                            if (row) row.remove();
                        } else {
                            alert("Error: " + data.message);
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("An unexpected error occurred.");
                    });
            }
        }

        $(window).on("load resize ", function() {
        var scrollWidth = $('.tbl-content').width() - $('.tbl-content table').width();
        $('.tbl-header').css({'padding-right':scrollWidth});
        }).resize();
    </script>