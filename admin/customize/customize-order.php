<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

// Define how many results per page
$results_per_page = 8;

// Find out the total number of orders
$total_query = $pdo->prepare("SELECT COUNT(*) FROM custom_order");
$total_query->execute();
$total_orders = $total_query->fetchColumn();

// Calculate total pages
$total_pages = ceil($total_orders / $results_per_page);

// Get the current page number from the query string, default is 1
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Prevent out-of-range page numbers
$current_page = max(1, min($current_page, $total_pages));

// Determine the starting limit number for the SQL query
$starting_limit = ($current_page - 1) * $results_per_page;

// Fetch data with limit and offset
$query = $pdo->prepare(
    "SELECT 
        co.order_id,
        co.customer_name,
        co.customer_email,
        co.shipping_address,
        co.order_date,
        GROUP_CONCAT(DISTINCT CONCAT(coi.flower_type, ' (', coi.num_flowers, ')') SEPARATOR '<br>') AS product_details,
        GROUP_CONCAT(DISTINCT coi.container_type SEPARATOR ', ') AS container_types,
        GROUP_CONCAT(DISTINCT coi.container_color SEPARATOR ', ') AS container_colors,
        GROUP_CONCAT(DISTINCT coi.remarks SEPARATOR '<br>') AS remarks,
        cp.payment_method,
        cp.amount_paid,
        cp.payment_status,
        cp.shipping_status,
        GROUP_CONCAT(DISTINCT ci.expected_image SEPARATOR ', ') AS expected_images,
        GROUP_CONCAT(DISTINCT cf.final_image SEPARATOR ', ') AS final_images
    FROM custom_order co
    LEFT JOIN custom_orderitems coi ON co.order_id = coi.order_id
    LEFT JOIN custom_payment cp ON co.order_id = cp.order_id
    LEFT JOIN custom_images ci ON co.order_id = ci.order_id
    LEFT JOIN custom_finalimages cf ON co.order_id = cf.order_id
    GROUP BY co.order_id
    ORDER BY co.order_id DESC
    LIMIT :starting_limit, :results_per_page"
);

$query->bindValue(':starting_limit', $starting_limit, PDO::PARAM_INT);
$query->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
$query->execute();
$orders = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Order Dashboard</title>
    <style>
        /* Keep the existing CSS you provided */
@import url(https://db.onlinewebfonts.com/c/90ac3b18aaef9f2db3ac8e062c7a033b?family=NudMotoya+Maru+W55+W5);

:root {
    --pink: #e84393;
}

body {
    font-family: "NudMotoya Maru W55 W5", sans-serif;
    background-color: #f9f9f9;
    color: #333;
    margin: 15px; /* Reduced margin */
    line-height: 1.4; /* Slightly reduced line height */
}

h3 {
    text-align: center;
    margin-bottom: 15px; /* Reduced margin */
    color: #444;
    font-size: 2.5rem; /* Slightly smaller font size */
}

.container {
    width: 85%; /* Reduced width slightly */
    margin: auto;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px; /* Reduced margin */
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Reduced shadow */
    font-family: "NudMotoya Maru W55 W5", sans-serif;
}

.custom-table th,
.custom-table td {
    padding: 12px; /* Reduced padding */
    text-align: center;
}

.custom-table th {
    background-color: var(--pink);
    color: #fff;
    text-transform: uppercase;
    font-size: 0.85rem; /* Slightly smaller font size */
    letter-spacing: 0.8px; /* Slightly reduced letter spacing */
}

.custom-table tr:nth-child(odd) {
    background-color: #f9f9f9;
}

.custom-table tr:nth-child(even) {
    background-color: #fff;
}

.custom-table tr:hover {
    background-color: #f1f1f1;
}

.btn {
    padding: 6px 12px; /* Reduced padding */
    font-size: 0.8rem; /* Smaller font size */
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
}

.btn-secondary {
    background-color: #555;
    color: #fff;
}

.btn-secondary:hover {
    background-color: #444;
}

.btn-info {
    background-color: #007bff;
    color: #fff;
}

.btn-info:hover {
    background-color: #0056b3;
}

.btn-danger {
    background-color: #dc3545;
    color: #fff;
}

.btn-danger:hover {
    background-color: #a71d2a;
}

.form-control {
    padding: 4px 8px; /* Reduced padding */
    font-size: 0.85rem; /* Slightly smaller font size */
    font-family: "NudMotoya Maru W55 W5", sans-serif;
}

.form-control select {
    width: 100%;
}

/* Pagination Styles */
.pagination {
    text-align: center;
    margin-top: 15px; /* Reduced margin */
}

.pagination a {
    margin: 0 4px; /* Reduced margin */
    padding: 6px 10px; /* Reduced padding */
    background-color: #ddd;
    color: #333;
    text-decoration: none;
    border-radius: 4px;
}

.pagination a:hover {
    background-color: #ccc;
}

.pagination .active {
    background-color: #007bff;
    color: white;
}
    </style>
</head>

<body>
    <div class="container">
        <h3>Custom Order Dashboard</h3>
        <div class="d-flex justify-content-start mb-3">
            <a href="../dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        <table class="custom-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Product Details</th>
                    <th>Container</th>
                    <th>Remarks</th>
                    <th>Payment Info</th>
                    <th>Paid Amount</th>
                    <th>Payment Status</th>
                    <th>Shipping Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $index => $order): ?>
                    <tr>
                        <td><?= $starting_limit + $index + 1; ?></td>
                        <td>
                            <strong>Id:</strong> <?= htmlspecialchars($order['order_id']); ?><br>
                            <strong>Name:</strong> <?= htmlspecialchars($order['customer_name']); ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($order['customer_email']); ?>
                        </td>
                        <td><?= $order['product_details'] ?? 'N/A'; ?></td>
                        <td>
                            <strong>Type:</strong> <?= htmlspecialchars($order['container_types'] ?? 'N/A'); ?><br>
                            <strong>Color:</strong> <?= htmlspecialchars($order['container_colors'] ?? 'N/A'); ?>
                        </td>
                        <td><?= htmlspecialchars($order['remarks'] ?? 'N/A'); ?></td>
                        <td>
                            <strong>Method:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'N/A'); ?><br>
                            <strong>Date:</strong> <?= htmlspecialchars($order['order_date'] ?? 'N/A'); ?>
                        </td>
                        <td>₱<?= number_format($order['amount_paid'] ?? 0, 2); ?></td>
                        <td>
                            <select class="form-control change-status" data-order-id="<?= $order['order_id']; ?>" data-field="payment_status">
                                <option value="Pending" <?= $order['payment_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Paid" <?= $order['payment_status'] == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                <option value="Failed" <?= $order['payment_status'] == 'Failed' ? 'selected' : ''; ?>>Failed</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control change-status" data-order-id="<?= $order['order_id']; ?>" data-field="shipping_status">
                                <option value="Pending" <?= $order['shipping_status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="Shipped" <?= $order['shipping_status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="Delivered" <?= $order['shipping_status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="Cancelled" <?= $order['shipping_status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="ReadyForPickup" <?= $order['shipping_status'] == 'ReadyForPickup' ? 'selected' : ''; ?>>Ready For Pickup</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm view-images" data-order-id="<?= $order['order_id']; ?>"
                                data-expected-images="<?= htmlspecialchars($order['expected_images']); ?>"
                                data-final-images="<?= htmlspecialchars($order['final_images']); ?>">
                                View Pictures
                            </button>
                            <button class="btn btn-danger btn-sm delete-order" data-order-id="<?= $order['order_id']; ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1; ?>" class="btn btn-secondary">Previous</a>
            <?php endif; ?>

            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                <a href="?page=<?= $page; ?>" class="btn <?= $page == $current_page ? 'active' : ''; ?>">
                    <?= $page; ?>
                </a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1; ?>" class="btn btn-secondary">Next</a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
