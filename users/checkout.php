<?php
require 'header.php';


// Redirect to login if the user is not logged in
if (!isset($_SESSION['customer']['cust_id'])) {
    header("Location: login.php");
    exit;
  }

// Initialize total amount
$subtotal = 0;

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
                            <h3 class="special">Shipping Address"</h3>
                            <table class="table table-responsive table-bordered table-hover table-striped bill-address">
                                <tr>
                                    <td>Full Name</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_name']; ?></p></td>
                                </tr>
                                <tr>
                                    <td>Phone Number</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>
                                        <?php echo nl2br($_SESSION['customer']['cust_s_address']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>City</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_city']; ?></td>
                                </tr>
                                <tr>
                                    <td>Zip Code</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_zip']; ?></td>
                                </tr> 
                            </table>
                        </div>
                    </div>                    
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
