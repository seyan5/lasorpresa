<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Dashboard</title>
  <link rel="stylesheet" href="order.css?v.1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .action-btn {
      margin-top: 10px;
    }

    .completed {
      background-color: #d4edda;
    }

    .pending {
      background-color: #ffeeba;
    }
  </style>
</head>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Dashboard</title>
  <link rel="stylesheet" href="order.css">
</head>
<div class="header">
            <a href="admin/dashbnoard.php" class="back-link">
                <span class="back-arrow">&lt;</span> Back to Cart
            </a>
  </div>
<body>
  
<h1>Order Dashboard</h1>
  <div class="container">  
    <table>
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
        <?php
        // Query to join the relevant tables
        $stmt = $pdo->query("SELECT 
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
                            o.order_id
                        FROM 
                            customer c
                        JOIN orders o ON c.cust_id = o.customer_id
                        JOIN order_items oi ON o.order_id = oi.order_id
                        JOIN product p ON oi.product_id = p.p_id
                        JOIN payment pay ON o.order_id = pay.order_id");

        // Fetch all orders
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as $index => $order) {
          ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td>
              <div class="customer-details">
                <strong>Id:</strong> <?= $order['cust_id'] ?><br>
                <strong>Name:</strong> <?= htmlspecialchars($order['cust_name']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($order['cust_email']) ?>
              </div>
            </td>
            <td>
              <div class="product-details">
                <strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?><br>
                <strong>Quantity:</strong> <?= $order['quantity'] ?><br>
                <strong>Unit Price:</strong> $<?= number_format($order['unit_price'], 2) ?>
              </div>
            </td>
            <td>
              <div class="payment-details">
                <strong>Payment Method:</strong> <?= $order['payment_method'] ?><br>
                <strong>Payment Id:</strong> <?= $order['payment_id'] ?><br>
                <strong>Date:</strong> <?= $order['payment_date'] ?>
              </div>
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
        <?php } ?>
      </tbody>
    </table>
  </div>

  <script>
    function updateOrderStatus(orderId) {
      const paymentStatus = document.getElementById(`payment-status-${orderId}`).value;
      const shippingStatus = document.getElementById(`shipping-status-${orderId}`).value;

      fetch('order-change-status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          order_id: orderId,
          payment_status: paymentStatus,
          shipping_status: shippingStatus,
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
        .catch((error) => {
          console.error('Error:', error);
          alert('An unexpected error occurred.');
        });
    }

    function deleteOrder(orderId) {
      if (confirm('Are you sure you want to delete this order?')) {
        fetch('order-delete.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: new URLSearchParams({
            order_id: orderId,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              alert(data.message);
              // Remove the row from the table on success
              const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
              if (row) row.remove();
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch((error) => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
          });
      }
    }

  </script>
</body>

</html>