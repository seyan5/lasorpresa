<?php
require_once('conn.php');

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

<?php
if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Update the viewed status for this notification
    $updateStatement = $pdo->prepare("UPDATE payment SET viewed = 1 WHERE order_id = :order_id");
    $updateStatement->execute(['order_id' => $orderId]);
}
?>


<?php include('navuser.php'); ?>
<style>
@import url(https://db.onlinewebfonts.com/c/90ac3b18aaef9f2db3ac8e062c7a033b?family=NudMotoya+Maru+W55+W5);
:root {
    --pink: #e84393;
}
/* General Styles */
body {
    font-family: "NudMotoya Maru W55 W5", Arial, sans-serif;
    margin: 20px;
    line-height: 1.8;
    background-color: #f9f9f9;
    color: #333;
    font-size: 1.2rem; /* Increased base font size */
}

h1 {
    text-align: center;
    color: #444;
    margin-bottom: 20px;
    margin-top: 15rem;
    font-size: 2.5rem; /* Larger font for main heading */
}

h2 {
    color: #555;
    margin-top: 20px;
    margin-bottom: 10px;
    font-size: 2rem; /* Larger font for subheadings */
}

/* Table Styles */
.table {
    width: 80%;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #fff;
    border: 1px solid #ddd;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
    font-size: 1.4rem; /* Larger font for table */
    font-family: "NudMotoya Maru W55 W5", Arial, sans-serif;
    font-weight: bold;
}

.table th, .table td {
    padding: 20px; /* Slightly increased padding for clarity */
    text-align: center;
    border: 1px solid #ddd;
}

.table th {
    background-color: var(--pink);
    color: #fff;
    text-transform: uppercase;
    font-size: 1.5rem; /* Larger font for table headers */
    letter-spacing: 1px;
}

.table tbody tr:nth-child(odd) {
    background-color: #f9f9f9;
}

.table tbody tr:nth-child(even) {
    background-color: #fff;
}

.table tbody tr:hover {
    background-color: #f1f1f1;
}

/* Image Styling */
.image img {
    max-width: 120px; /* Slightly larger images */
    height: auto;
    display: block;
    margin: 0 auto;
    border-radius: 4px;
}

/* Responsive Design */
@media (max-width: 768px) {
    body {
        font-size: 1rem; /* Adjusted for smaller screens */
        margin: 10px;
    }

    .table {
        font-size: 1rem;
    }

    img {
        max-width: 100px;
    }
}


</style>
<body>
<?php include('back.php'); ?>
    <h1>Order Details</h1>

    <?php if ($orderDetails): ?>
    <div class="order-details">
        <table class="table">
        <thead>
          <tr>
            <th>Product Details</th>
            <th>Payment Information</th>
            <th>Paid Amount</th>
            <th>Payment Status</th>
            <th>Shipping Status</th>
          </tr>
        </thead>
        <tbody>
              <tr>
                <td class="image">        
                    <img src="../admin/uploads/<?php echo htmlspecialchars($productImage); ?>" alt="<?php echo htmlspecialchars($productName); ?>"><br>
                    <strong>Product:</strong> 
<a href="product-details.php?product_id=<?= urlencode($productId) ?>">
    <?= htmlspecialchars($productName) ?>
</a><br>

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
    </div>
    <?php endif; ?>

</body>
</html>
