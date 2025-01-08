<?php
require '../header.php'; // Assuming you have a header file that manages session, security, etc.



// Fetch orders from the database
$statement = $pdo->prepare("SELECT * FROM orders ORDER BY created_at DESC");
$statement->execute();
$orders = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Orders</title>
  <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
  <div class="container">
    <header class="header">
      <h1>Manage Orders</h1>
    </header>

    <main class="main-content">
      <table border="1" style="width:100%; border-collapse: collapse;">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Address</th>
            <th>City</th>
            <th>Postal Code</th>
            <th>Phone</th>
            <th>Total Price</th>
            <th>Order Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td><?php echo $order['order_id']; ?></td>
              <td><?php echo $order['full_name']; ?></td>
              <td><?php echo $order['address']; ?></td>
              <td><?php echo $order['city']; ?></td>
              <td><?php echo $order['postal_code']; ?></td>
              <td><?php echo $order['phone']; ?></td>
              <td><?php echo "$" . number_format($order['total'], 2); ?></td>
              <td><?php echo $order['created_at']; ?></td>
              <td>
                <!-- You can add actions here like viewing details or updating status if needed -->
                <form action="update-order-status.php" method="POST">
                  <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                  <select name="status">
                    <option value="Pending">Pending</option>
                    <option value="Completed">Completed</option>
                    <option value="Shipped">Shipped</option>
                  </select>
                  <button type="submit">Update Status</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>
