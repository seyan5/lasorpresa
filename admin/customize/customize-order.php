<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

// Fetch data from custom_order, custom_orderitems, and custom_payment tables
$query = $pdo->prepare(
    "SELECT 
        co.order_id,
        co.customer_name,
        co.customer_email,
        co.shipping_address,
        co.order_date, -- Include order_date here
        GROUP_CONCAT(CONCAT(coi.flower_type, ' (', coi.num_flowers, ')') SEPARATOR '<br>') AS product_details,
        coi.container_type,
        coi.container_color,
        coi.remarks,
        cp.payment_method,
        cp.amount_paid,
        cp.payment_status,
        cp.shipping_status
    FROM custom_order co
    LEFT JOIN custom_orderitems coi ON co.order_id = coi.order_id
    LEFT JOIN custom_payment cp ON co.order_id = cp.order_id
    GROUP BY co.order_id, coi.container_type, coi.container_color, coi.remarks, cp.payment_method, cp.amount_paid, cp.payment_status, cp.shipping_status"
);

$query->execute();
$orders = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">Order Dashboard</h3>
        <!-- Back Button -->
        <div class="d-flex justify-content-start mb-3">
            <a href="../dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Product Details</th>
                    <th>Container</th>
                    <th>Remarks</th>
                    <th>Payment Information</th>
                    <th>Paid Amount</th>
                    <th>Payment Status</th>
                    <th>Shipping Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $index => $order): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td>
                            <strong>Id:</strong> <?= htmlspecialchars($order['order_id'] ?? 'N/A'); ?><br>
                            <strong>Name:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'N/A'); ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($order['customer_email'] ?? 'N/A'); ?>
                        </td>
                        <td>
                            <?= $order['product_details'] ?? 'N/A'; ?>
                        </td>
                        <td>
                            <strong>Type:</strong> <?= htmlspecialchars($order['container_type'] ?? 'N/A'); ?><br>
                            <strong>Color:</strong> <?= htmlspecialchars($order['container_color'] ?? 'N/A'); ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($order['remarks'] ?? 'N/A'); ?>
                        </td>
                        <td>
                            <strong>Method:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'N/A'); ?><br>
                            <strong>Date:</strong> <?= htmlspecialchars($order['order_date'] ?? 'N/A'); ?><br>
                        </td>
                        <td>
                            ₱<?= number_format($order['amount_paid'] ?? 0, 2); ?>
                        </td>
                        <td>
                            <select class="form-control payment-status" data-order-id="<?= $order['order_id']; ?>">
                                <option value="Pending" <?= $order['payment_status'] === 'Pending' ? 'selected' : ''; ?>>
                                    Pending</option>
                                <option value="Paid" <?= $order['payment_status'] === 'Paid' ? 'selected' : ''; ?>>Paid
                                </option>
                                <option value="Failed" <?= $order['payment_status'] === 'Failed' ? 'selected' : ''; ?>>Failed
                                </option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control shipping-status" data-order-id="<?= $order['order_id']; ?>">
                                <option value="Pending" <?= $order['shipping_status'] === 'Pending' ? 'selected' : ''; ?>>
                                    Pending</option>
                                <option value="Shipped" <?= $order['shipping_status'] === 'Shipped' ? 'selected' : ''; ?>>
                                    Shipped</option>
                                <option value="Delivered" <?= $order['shipping_status'] === 'Delivered' ? 'selected' : ''; ?>>
                                    Delivered</option>
                                <option value="Ready for Pickup" <?= $order['shipping_status'] === 'Ready for Pickup' ? 'selected' : ''; ?>>Ready for Pickup</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-order"
                                data-order-id="<?= $order['order_id']; ?>">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Handle payment status update
        $('.payment-status').on('change', function () {
            const orderId = $(this).data('order-id');
            const status = $(this).val();

            // AJAX request to update payment status
            $.post('customize-order-change-status.php', { order_id: orderId, payment_status: status }, function (response) {
                alert(response.message);
            }, 'json');
        });

        // Handle shipping status update
        $('.shipping-status').on('change', function () {
            const orderId = $(this).data('order-id');
            const status = $(this).val();

            // AJAX request to update shipping status
            $.post('customize-order-change-status.php', { order_id: orderId, shipping_status: status }, function (response) {
                alert(response.message);
            }, 'json');
        });

        // Handle delete order
        $('.delete-order').on('click', function () {
            const orderId = $(this).data('order-id');

            if (confirm('Are you sure you want to delete this order?')) {
                // AJAX request to delete the order
                $.post('customize-order-delete.php', { order_id: orderId }, function (response) {
                    if (response.success) {
                        alert(response.message);
                        location.reload(); // Reload the page to reflect changes
                    } else {
                        alert(response.message);
                    }
                }, 'json');
            }
        });
    </script>
</body>

</html>