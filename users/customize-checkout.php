<?php
require("conn.php");

// Ensure user is logged in
if (!isset($_SESSION['customer']['cust_id'])) {
    echo "You need to log in to proceed to checkout.";
    exit;
}

// Fetch customer details
$customer_id = $_SESSION['customer']['cust_id'];
$stmt = $pdo->prepare("SELECT cust_name, cust_email, cust_address FROM customer WHERE cust_id = :cust_id");
$stmt->execute(['cust_id' => $customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$customer) {
    echo "Customer details not found.";
    exit;
}

// Fetch customization session data
if (!isset($_SESSION['customization'])) {
    echo "No customization found. Please go back and customize your arrangement.";
    exit;
}
$customization = $_SESSION['customization'];

// Calculate total price
$total_price = 0;
foreach ($customization as $item) {
    $stmt = $pdo->prepare("SELECT price FROM flowers WHERE id = :flower_id");
    $stmt->execute(['flower_id' => $item['flower_type']]);
    $flower = $stmt->fetch(PDO::FETCH_ASSOC);
    $flower_price = $flower['price'] ?? 0;
    $total_price += ($flower_price * $item['num_flowers']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $reference_number = $_POST['reference_number'] ?? null;
    $amount_paid = $_POST['amount_paid'] ?? $total_price;

    // Step 1: Insert into `custom_order` table
    $stmt = $pdo->prepare("INSERT INTO custom_order (cust_id, customer_name, customer_email, shipping_address, total_price, order_date) VALUES (:cust_id, :customer_name, :customer_email, :shipping_address, :total_price, NOW())");
    $stmt->execute([
        'cust_id' => $customer_id,
        'customer_name' => $customer['cust_name'],
        'customer_email' => $customer['cust_email'],
        'shipping_address' => $customer['cust_address'],
        'total_price' => $total_price,
    ]);

    // Step 2: Get the last inserted `order_id`
    $order_id = $pdo->lastInsertId();

    // Step 3: Insert into `custom_payment` table
    $stmt = $pdo->prepare("INSERT INTO custom_payment (order_id, customer_name, customer_email, amount_paid, payment_method, reference_number, payment_status, shipping_status, created_at) VALUES (:order_id, :customer_name, :customer_email, :amount_paid, :payment_method, :reference_number, 'Pending', 'Pending', NOW())");
    $stmt->execute([
        'order_id' => $order_id,
        'customer_name' => $customer['cust_name'],
        'customer_email' => $customer['cust_email'],
        'amount_paid' => $amount_paid,
        'payment_method' => $payment_method,
        'reference_number' => $reference_number,
    ]);

    echo "<script>alert('Payment successful!');</script>";
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h3>Checkout</h3>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer['cust_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['cust_email']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['cust_address']); ?></p>
        <p><strong>Total Price:</strong> ₱<?php echo number_format($total_price, 2); ?></p>

        <form id="checkout-form" method="POST">
            <label for="payment_method">Select Payment Method:</label><br>
            <input type="radio" name="payment_method" value="GCash" id="gcash" required> GCash<br>
            <input type="radio" name="payment_method" value="Cash on Pickup" id="cop" required> Cash on Pickup<br><br>

            <button type="button" class="btn btn-primary" id="proceed-payment">Proceed to Payment</button>
        </form>
    </div>

    <!-- GCash Modal -->
    <div class="modal fade" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gcashModalLabel">GCash Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Scan the QR Code to pay:</p>
                    <img src="path_to_qr_code_image" alt="GCash QR Code" style="width: 100%; height: auto;">
                    <form method="POST">
                        <input type="hidden" name="payment_method" value="gcash">
                        <label for="reference_number">Reference Number:</label>
                        <input type="text" name="reference_number" class="form-control" required>
                        <label for="amount_paid">Amount Paid:</label>
                        <input type="text" name="amount_paid" class="form-control" value="<?php echo $total_price; ?>"
                            readonly>
                        <button type="submit" class="btn btn-success mt-3">Done</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="copModal" tabindex="-1" aria-labelledby="copModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="copModalLabel">Cash on Pickup</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Please bring the exact amount of ₱<?php echo number_format($total_price, 2); ?> upon pickup.</p>
                    <form method="POST">
                        <input type="hidden" name="payment_method" value="cop">
                        <input type="hidden" name="reference_number" value="">
                        <input type="hidden" name="amount_paid" value="<?php echo $total_price; ?>">
                        <button type="submit" class="btn btn-success">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            $('#proceed-payment').on('click', function () {
                if ($('#gcash').is(':checked')) {
                    $('#gcashModal').modal('show');
                } else if ($('#cop').is(':checked')) {
                    $('#copModal').modal('show');
                } else {
                    alert('Please select a payment method.');
                }
            });
        });
    </script>
</body>

</html>