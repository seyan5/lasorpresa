<?php
require 'header.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['customer']['cust_id'])) {
    header("Location: login.php");
    exit;
}

// Initialize total amount
$subtotal = 0;

// Handle order placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Database connection (you may already have this)
    include 'header.php';

    // Insert the order into the orders table
    $customer_id = $_SESSION['customer']['cust_id'];
    $order_total = $subtotal; // Set the total price for the order
    $shipping = 0; // Shipping cost
    $status = 'Pending'; // Order status, can be updated later

    // Prepare the SQL statement to insert the order
    $stmt = $conn->prepare("INSERT INTO orders (cust_id, total_price, shipping, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $customer_id, $order_total, $shipping, $status);
    $stmt->execute();
    $order_id = $stmt->insert_id; // Get the inserted order's ID

    // Insert each item into the order_items table
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['id'];
        $product_name = $item['name'];
        $price = $item['price'];
        $quantity = $item['quantity'];
        $total_price = $price * $quantity;

        // Prepare the SQL statement to insert the order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, p_id, product_name, price, quantity, total_price) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdis", $order_id, $p_id, $product_name, $price, $quantity, $total_price);
        $stmt->execute();
    }

    // Clear the cart
    unset($_SESSION['cart']);

    // Redirect to a confirmation or order history page
    header("Location: order_confirmation.php?order_id=$order_id");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/checkout.css">
    <title>Checkout</title>
</head>
<body>
<div class="container">
    <h1>Checkout</h1>
    <div class="cart">
        <table>
            <thead>
                <tr>
                    <th>Photo</th>
                    <th>Product Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <tr>
                            <td>
                                <?php if (isset($item['image']) && $item['image']): ?>
                                    <img src="../admin/uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
                                <?php else: ?>
                                    <img src="path/to/default-image.jpg" alt="No image available" width="50">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>₱<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php $subtotal += $item['price'] * $item['quantity']; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <h3>Order Summary</h3>
        <p>Subtotal: <span>₱<?php echo number_format($subtotal, 2); ?></span></p>
        <p>Shipping: <span>₱0.00</span></p>
        <p>Total: <span>₱<?php echo number_format($subtotal, 2); ?></span></p>

        <div class="col-md-6">
            <h3 class="special">Shipping Address</h3>
            <table class="table table-responsive table-bordered table-hover table-striped bill-address">
                <tr>
                    <td>Full Name</td>
                    <td>
                        <?php echo isset($_SESSION['customer']['cust_s_name']) ? $_SESSION['customer']['cust_s_name'] : 'No name available'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Phone Number</td>
                    <td>
                        <?php echo isset($_SESSION['customer']['cust_s_phone']) ? $_SESSION['customer']['cust_s_phone'] : 'No phone number available'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td>
                        <?php echo isset($_SESSION['customer']['cust_s_address']) ? nl2br($_SESSION['customer']['cust_s_address']) : 'No address available'; ?>
                    </td>
                </tr>
                <tr>
                    <td>City</td>
                    <td>
                        <?php echo isset($_SESSION['customer']['cust_s_city']) ? $_SESSION['customer']['cust_s_city'] : 'No city available'; ?>
                    </td>
                </tr>
                <tr>
                    <td>Zip Code</td>
                    <td>
                        <?php echo isset($_SESSION['customer']['cust_s_zip']) ? $_SESSION['customer']['cust_s_zip'] : 'No zip code available'; ?>
                    </td>
                </tr> 
            </table>
            <form method="POST" action="">
                <button type="submit" name="place_order" class="place-order">Place Order</button>
            </form>
        </div>
    </div>                    
</div>

<style>
    .container {
        width: 90%;
        margin: auto;
        padding: 20px;
    }
    .cart table {
        width: 100%;
        border-collapse: collapse;
    }
    .cart table th, .cart table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }
    .cart table th {
        background-color: #f4f4f4;
    }
    .summary {
        margin-top: 20px;
    }
    .summary textarea {
        width: 100%;
        height: 100px;
        margin-top: 10px;
        padding: 10px;
    }
    .place-order {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }
    .place-order:hover {
        background-color: #0056b3;
    }
</style>
</body>
</html>
