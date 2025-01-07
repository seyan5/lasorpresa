<?php
session_start();
include("admin/inc/config.php");

// Ensure the admin is logged in (add authentication as needed)
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}

// Fetch all customers from the database
$stmt = $pdo->query("SELECT cust_id, cust_name, cust_email, cust_address, cust_city, cust_status FROM customer");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
</head>
<body>

<h2>Customer Management</h2>

<table border="1">
    <thead>
        <tr>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>City</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($customers as $customer): ?>
        <tr>
            <td><?php echo htmlspecialchars($customer['cust_id']); ?></td>
            <td><?php echo htmlspecialchars($customer['cust_name']); ?></td>
            <td><?php echo htmlspecialchars($customer['cust_email']); ?></td>
            <td><?php echo htmlspecialchars($customer['cust_address']); ?></td>
            <td><?php echo htmlspecialchars($customer['cust_city']); ?></td>
            <td>
                <!-- Button to change status -->
                <form action="change_status.php" method="POST">
                    <input type="hidden" name="cust_id" value="<?php echo $customer['cust_id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                        <option value="active" <?php echo ($customer['cust_status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($customer['cust_status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </form>
            </td>
            <td>
                <!-- Button to delete customer -->
                <form action="delete_customer.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                    <input type="hidden" name="cust_id" value="<?php echo $customer['cust_id']; ?>">
                    <button type="submit" name="delete_customer">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
