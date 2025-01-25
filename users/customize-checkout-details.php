<?php
require("conn.php");

// Check if customization session data and POST data exist
if (!isset($_SESSION['customization']) || empty($_POST['selected_customizations'])) {
    echo "No customizations selected. Redirecting to cart...";
    header("refresh:3;url=customize-cart.php");
    exit;
}

// Retrieve customization and customer data
$all_customizations = $_SESSION['customization'];
$selected_indices = $_POST['selected_customizations'];
$customer = $_SESSION['customer'];

// Filter selected customizations
$grouped_customization = [];
$total_price = 0;

foreach ($selected_indices as $index) {
    if (isset($all_customizations[$index])) {
        $customization = $all_customizations[$index];

        // Calculate total price for this customization
        $customization_price = 0;

        // Add container price
        $stmt = $pdo->prepare("SELECT container_name, price FROM container WHERE container_id = ?");
        $stmt->execute([$customization['container_type']]);
        $container = $stmt->fetch(PDO::FETCH_ASSOC);
        $container_name = $container['container_name'] ?? "Unknown Container";
        $container_price = $container['price'] ?? 0;
        $customization_price += $container_price;

        // Add flower prices
        $flower_details = [];
        foreach ($customization['flowers'] as $flower) {
            $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = ?");
            $stmt->execute([$flower['flower_type']]);
            $flower_data = $stmt->fetch(PDO::FETCH_ASSOC);

            $flower_name = $flower_data['name'] ?? "Unknown Flower";
            $flower_price = $flower_data['price'] ?? 0;
            $flower_quantity = $flower['num_flowers'] ?? 0;

            $flower_total = $flower_price * $flower_quantity;
            $customization_price += $flower_total;

            $flower_details[] = "{$flower_quantity}x {$flower_name} (₱{$flower_total})";
        }

        $total_price += $customization_price;

        // Fetch expected image
        $expected_image = !empty($customization['expected_image']) ? $customization['expected_image'] : 'default-image.jpg';

        $grouped_customization[] = [
            'container_name' => $container_name,
            'container_price' => $container_price,
            'flower_details' => $flower_details,
            'remarks' => $customization['remarks'] ?? 'None',
            'total_price' => $customization_price,
            'expected_image' => $expected_image, // Include expected image
        ];
    }
}

// Check if valid customizations exist
if (empty($grouped_customization)) {
    echo "No valid customizations found. Redirecting to cart...";
    header("refresh:3;url=customize-cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="../css/customize-checkout.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="header">
        <a href="customize-cart.php" class="back-link">
            <span class="back-arrow">&lt;</span> Back to Cart
        </a>
    </div>
    <div class="container">
        <div class="summary">
            <h3>Order Summary</h3>
            <ul>
                <?php foreach ($grouped_customization as $customization): ?>
                    <li>
                        <p><strong>Container Type:</strong>
                            <?php echo htmlspecialchars($customization['container_name']); ?>
                            (₱<?php echo number_format($customization['container_price'], 2); ?>)</p>
                        <p><strong>Flowers:</strong>
                        <ul>
                            <?php foreach ($customization['flower_details'] as $flower): ?>
                                <li><?php echo htmlspecialchars($flower); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        </p>
                        <p><strong>Remarks:</strong> <?php echo htmlspecialchars($customization['remarks']); ?></p>
                        <p><strong>Subtotal:</strong> ₱<?php echo number_format($customization['total_price'], 2); ?></p>
                        <p><strong>Expected Image:</strong></p>
                        <div class="image-preview">
                            <img src="uploads/<?php echo htmlspecialchars($customization['expected_image']); ?>" alt="Expected Output" style="width: 200px; height: auto;">
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p><strong>Total Price:</strong> ₱<?php echo number_format($total_price, 2); ?></p>
        </div>

        <div class="customer-info">
            <h3>Shipping Information</h3>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($customer['cust_name']); ?></p>
            <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($customer['cust_phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($customer['cust_address']); ?></p>

            <form id="checkout-form" action="customize-checkout.php" method="POST">
                <input type="hidden" name="selected_customizations"
                    value="<?php echo htmlspecialchars(json_encode($selected_indices)); ?>">
                <label for="payment_method">Mode of Payment:</label>
                <div>
                    <input type="radio" name="payment_method" value="gcash" id="gcash" required>
                    <label for="gcash">GCash</label>
                    <input type="radio" name="payment_method" value="cop" id="cop" required>
                    <label for="cop">Cash on Pickup</label>
                </div>
                <button type="button" class="checkout" id="proceed-payment">Checkout</button>
            </form>
        </div>
    </div>

    <!-- GCash Modal -->
    <div class="modal" id="gcashModal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                GCash Payment
                <button class="modal-close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Scan the QR Code to pay:</p>
                <img src="../images/gcashqr.jpg" alt="GCash QR Code" style="width: 100%; height: auto;">
                <form id="gcash-form" action="customize-checkout.php" method="POST">
                    <input type="hidden" name="payment_method" value="gcash">
                    <input type="hidden" name="selected_customizations"
                        value="<?php echo htmlspecialchars(json_encode($selected_indices)); ?>">
                    <label for="reference_number">Reference Number:</label>
                    <input type="text" name="reference_number" class="form-control" required>
                    <label for="amount_paid">Amount Paid:</label>
                    <input type="text" name="amount_paid" class="form-control"
                        value="<?php echo htmlspecialchars($total_price); ?>" readonly>
                    <button type="submit" class="btn btn-success">Done</button>
                </form>
            </div>
        </div>
    </div>

    <!-- COP Modal -->
    <div class="modal" id="copModal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                Cash on Pickup
                <button class="modal-close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Your order is confirmed with Cash on Pickup.</p>
                <form id="cop-form" action="customize-checkout.php" method="POST">
                    <input type="hidden" name="payment_method" value="cop">
                    <input type="hidden" name="selected_customizations"
                        value="<?php echo htmlspecialchars(json_encode($selected_indices)); ?>">
                    <label for="amount_paid">Amount to Pay Upon Pickup:</label>
                    <input type="text" name="amount_paid" class="form-control"
                        value="<?php echo htmlspecialchars($total_price); ?>" readonly>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#proceed-payment').on('click', function () {
                if ($('#gcash').is(':checked')) {
                    $('#gcashModal').fadeIn();
                } else if ($('#cop').is(':checked')) {
                    $('#copModal').fadeIn();
                } else {
                    alert('Please select a payment method.');
                }
            });

            $('.modal-close').on('click', function () {
                $(this).closest('.modal').fadeOut();
            });
        });
    </script>
</body>

</html>
