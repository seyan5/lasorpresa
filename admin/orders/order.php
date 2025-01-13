<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Get current page, default to 1
$perPage = 10; // Set the number of orders per page
$offset = ($page - 1) * $perPage; // Calculate the offset

// Query to fetch orders and payment info, joining on order_id
$stmt = $pdo->prepare("SELECT 
                            c.cust_id, 
                            c.cust_name, 
                            c.cust_email, 
                            oi.order_id, 
                            GROUP_CONCAT(p.name ORDER BY oi.product_id) AS product_names, 
                            GROUP_CONCAT(oi.quantity ORDER BY oi.product_id) AS quantities, 
                            GROUP_CONCAT(p.current_price ORDER BY oi.product_id) AS unit_prices, 
                            pay.payment_method, 
                            pay.payment_id, 
                            pay.created_at AS payment_date, 
                            pay.amount_paid, 
                            pay.shipping_status, 
                            pay.payment_status
                        FROM 
                            customer c
                        JOIN orders o ON c.cust_id = o.customer_id
                        JOIN order_items oi ON o.order_id = oi.order_id
                        JOIN product p ON oi.product_id = p.p_id
                        JOIN payment pay ON o.order_id = pay.order_id
                        GROUP BY o.order_id
                        ORDER BY o.order_id DESC
                        LIMIT :limit OFFSET :offset");
$stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total orders count to calculate pages
$totalOrdersStmt = $pdo->query("SELECT COUNT(DISTINCT o.order_id) FROM orders o");
$totalOrders = $totalOrdersStmt->fetchColumn();
$totalPages = ceil($totalOrders / $perPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .form-select {
      width: auto;
    }
  </style>
</head>
<body>
  <div class="container my-4">
    <h1 class="text-center">Order Dashboard</h1>
    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Customer</th>
          <th>Product Details</th>
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
            <td><?= $index + 1 ?></td>
            <td>
              <strong>Id:</strong> <?= $order['cust_id'] ?><br>
              <strong>Name:</strong> <?= htmlspecialchars($order['cust_name']) ?><br>
              <strong>Email:</strong> <?= htmlspecialchars($order['cust_email']) ?>
            </td>
            <td>
              <strong>Products:</strong> <?= htmlspecialchars($order['product_names']) ?><br>
              <strong>Quantities:</strong> <?= htmlspecialchars($order['quantities']) ?><br>
              <strong>Unit Prices:</strong> <?= htmlspecialchars($order['unit_prices']) ?><br>
              <strong>Total Price:</strong> $<?= number_format(array_sum(array_map(function($quantity, $price) { return $quantity * $price; }, explode(',', $order['quantities']), explode(',', $order['unit_prices']))), 2) ?>
            </td>
            <td>
              <strong>Payment Method:</strong> <?= $order['payment_method'] ?><br>
              <strong>Payment Id:</strong> <?= $order['payment_id'] ?><br>
              <strong>Date:</strong> <?= $order['payment_date'] ?>
            </td>
            <td>$<?= number_format($order['amount_paid'], 2) ?></td>
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
            <td>
              <button class="btn btn-primary" onclick="updateOrderStatus(<?= $order['order_id'] ?>)">Update</button>
              <button class="btn btn-danger" onclick="deleteOrder(<?= $order['order_id'] ?>)">Delete</button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Pagination controls -->
    <div class="d-flex justify-content-between">
      <div>
        <?php if ($page > 1): ?>
          <a href="?page=<?= $page - 1 ?>" class="btn btn-secondary">Previous</a>
        <?php endif; ?>
      </div>
      <div>
        <?php if ($page < $totalPages): ?>
          <a href="?page=<?= $page + 1 ?>" class="btn btn-secondary">Next</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>

<script>
  function updateOrderStatus(orderId) {
    // Ask for confirmation before updating the status
    if (!confirm("Are you sure you want to update the order status?")) {
      return; // If user cancels, stop further execution
    }

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
        // Reload the page to reflect updated status
        location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch((error) => {
      console.error('Error:', error);
      alert('An unexpected error occurred.');
    });

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
        // Reload the page to reflect updated status
        location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch((error) => {
      console.error('Error:', error);
      alert('An unexpected error occurred.');
    });
  }
</script>
</html>
