<?php
// Include necessary files for database connection and session management
include("../admin/inc/config.php");
include("../admin/inc/functions.php");

// Fetch the order_id and product_id from the URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

if ($order_id && $product_id) {
    // Query to get the order, product, and payment details
    $stmt = $pdo->prepare("
        SELECT o.order_id, o.customer_id, c.cust_name, 
               p.payment_status, p.shipping_status, p.amount_paid, 
               oi.product_id, oi.quantity, oi.price, pr.name AS product_name, pr.featured_photo, p.payment_method, p.created_at
        FROM orders o
        JOIN payment p ON o.order_id = p.order_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product pr ON oi.product_id = pr.p_id
        JOIN customer c ON o.customer_id = c.cust_id
        WHERE o.order_id = :order_id AND oi.product_id = :product_id
    ");
    $stmt->execute(['order_id' => $order_id, 'product_id' => $product_id]);
    $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($orderDetails) {
        // Extract order and product details
        $paymentStatus = $orderDetails['payment_status'];
        $shippingStatus = $orderDetails['shipping_status'];
        $amountPaid = $orderDetails['amount_paid'];
        $quantity = $orderDetails['quantity'];
        $price = $orderDetails['price'];
        $productName = $orderDetails['product_name'];
        $productImage = $orderDetails['featured_photo'];
        $customerId = $orderDetails['customer_id'];
        $fullName = $orderDetails['cust_name'];
        $paymentMethod = $orderDetails['payment_method'];
        $createdAt = $orderDetails['created_at'];
    } else {
        // Handle case if order is not found
        echo "Order not found.";
    }
} else {
    echo "Invalid order or product ID.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="path_to_your_stylesheet.css">
    <style>
        /* Simple styling for the table */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        img {
            width: 100px;
            height: auto;
        }
    </style>
</head>
<body>
    <h1>Order Details</h1>

    <?php if ($orderDetails): ?>
    <div class="order-details">
        <!-- Customer Details -->
        <h2>Customer Information</h2>
        <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>Customer</th>
            <th>Product Details</th>
            <th>Payment Information</th>
            <th>Paid Amount</th>
            <th>Payment Status</th>
            <th>Shipping Status</th>
          </tr>
        </thead>
        <tbody>

              <tr>
                <td>
                  <strong>Id:</strong> <?php echo htmlspecialchars($customerId); ?><br>
                  <strong>Name:</strong> <?php echo htmlspecialchars($fullName); ?><br>
                </td>
                <td>
                    <img src="../admin/uploads/<?php echo htmlspecialchars($productImage); ?>" alt="<?php echo htmlspecialchars($productName); ?>"><br>
                    <strong>Product:</strong> <?php echo htmlspecialchars($productName); ?><br>
                    <strong>Quantity:</strong> <?php echo htmlspecialchars($quantity); ?><br>
                    <strong>Unit Price:</strong> ₱<?php echo number_format($price, 2); ?>
                </td>
                <td>
                  <strong>Payment Method:</strong> <?php echo htmlspecialchars($paymentMethod); ?><br>
                  <strong>Date:</strong> <?php echo htmlspecialchars($createdAt); ?>
                </td>
                <td>₱<?php echo number_format($amountPaid, 2); ?></td>
                <td>
                <strong>Payment Status:</strong> <?php echo htmlspecialchars($paymentStatus); ?>
                </td>
                <td>
                <strong>Shipping Status:</strong> <?php echo htmlspecialchars($shippingStatus); ?>
                </td>
              </tr>
        </tbody>
      </table>
      </table>
    </div>
    <?php endif; ?>

</body>
</html>
