<?php
require("conn.php");

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

// Retrieve customization and total price from session
$customization = $_SESSION['customization'] ?? null;
$total_price = $_SESSION['total_price'] ?? 0;

if (!$customization || !is_array($customization)) {
    echo "No customization details found. Please go back and customize your arrangement.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $reference_number = $_POST['reference_number'] ?? null;
    $amount_paid = $_POST['amount_paid'] ?? $total_price;

    try {
        $pdo->beginTransaction();

        // Insert into `custom_order`
        $stmt = $pdo->prepare("
            INSERT INTO custom_order (cust_id, customer_name, customer_email, shipping_address, total_price, order_date)
            VALUES (:cust_id, :customer_name, :customer_email, :shipping_address, :total_price, NOW())
        ");
        $stmt->execute([
            'cust_id' => $customer_id,
            'customer_name' => $customer['cust_name'],
            'customer_email' => $customer['cust_email'],
            'shipping_address' => $customer['cust_address'],
            'total_price' => $total_price,
        ]);

        $order_id = $pdo->lastInsertId();

        // Insert order items and images
        foreach ($customization as $item) {
            $flower_name = $item['flower_type'] ?? 'Unknown';
            $flower_price = $item['flower_price'] ?? 0;
            $num_flowers = $item['num_flowers'] ?? 0;
            $container_name = $item['container_type'] ?? 'Unknown';
            $container_price = $item['container_price'] ?? 0;
            $color_name = $item['container_color'] ?? 'Unknown';
            $item_total_price = $flower_price * $num_flowers;

            $stmt = $pdo->prepare("
                INSERT INTO custom_orderitems (
                    order_id, flower_type, num_flowers, container_type, container_color, flower_price, container_price, color_price, total_price, remarks
                ) VALUES (
                    :order_id, :flower_type, :num_flowers, :container_type, :container_color, :flower_price, :container_price, :color_price, :total_price, :remarks
                )
            ");
            $stmt->execute([
                'order_id' => $order_id,
                'flower_type' => $flower_name,
                'num_flowers' => $num_flowers,
                'container_type' => $container_name,
                'container_color' => $color_name,
                'flower_price' => $flower_price,
                'container_price' => $container_price,
                'color_price' => 0,
                'total_price' => $item_total_price,
                'remarks' => $item['remarks'] ?? '',
            ]);

            if (!empty($item['expected_image'])) {
                $stmt = $pdo->prepare("INSERT INTO custom_images (order_id, expected_image) VALUES (:order_id, :expected_image)");
                $stmt->execute([
                    'order_id' => $order_id,
                    'expected_image' => $item['expected_image'],
                ]);
            }
        }

        // Insert into `custom_payment`
        $stmt = $pdo->prepare("
            INSERT INTO custom_payment (order_id, customer_name, customer_email, amount_paid, payment_method, reference_number, payment_status, shipping_status, created_at)
            VALUES (:order_id, :customer_name, :customer_email, :amount_paid, :payment_method, :reference_number, 'Pending', 'Pending', NOW())
        ");
        $stmt->execute([
            'order_id' => $order_id,
            'customer_name' => $customer['cust_name'],
            'customer_email' => $customer['cust_email'],
            'amount_paid' => $amount_paid,
            'payment_method' => $payment_method,
            'reference_number' => $reference_number,
        ]);

        $pdo->commit();
        unset($_SESSION['customization']);
    unset($_SESSION['total_price']);

        echo "<script>alert('Payment successful!');</script>";
        echo "<script>window.location.href = 'customization.php';</script>";
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Failed to process the order: " . $e->getMessage();
        exit;
    }
}
$customization = $_SESSION['customization'];
$grouped_customization = [];
$total_price = 0;

foreach ($customization as $item) {
    $key = $item['container_type'] . '-' . $item['container_color'] . '-' . $item['remarks'];

    if (!isset($grouped_customization[$key])) {
        $stmt = $pdo->prepare("SELECT price FROM container WHERE container_id = :container_id");
        $stmt->execute(['container_id' => $item['container_type']]);
        $container = $stmt->fetch(PDO::FETCH_ASSOC);
        $container_price = $container['price'] ?? 0;

        $grouped_customization[$key] = [
            'container_type' => $item['container_type'],
            'container_color' => $item['container_color'],
            'remarks' => $item['remarks'] ?? 'No remarks provided',
            'flowers' => [],
            'container_price' => $container_price,
            'expected_image' => $item['expected_image'] ?? '/lasorpresa/images/default-image.jpg',
        ];

        $total_price += $container_price;
    }

    $grouped_customization[$key]['flowers'][] = $item;

    $stmt = $pdo->prepare("SELECT price FROM flowers WHERE id = :flower_id");
    $stmt->execute(['flower_id' => $item['flower_type']]);
    $flower = $stmt->fetch(PDO::FETCH_ASSOC);
    $flower_price = $flower['price'] ?? 0;

    $total_price += $flower_price * $item['num_flowers'];
}

$_SESSION['total_price'] = $total_price;
?>

<?php include('navuser.php'); ?>
<link rel="stylesheet" href="../css/customize-checkout.css?">


<style>
        /* General Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.4);
}

/* Modal Content */
.modal-dialog {
    background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Close Button */
.modal-close {
    color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
}

.modal-close:hover,
.modal-close:focus {
    color: black;
            text-decoration: none;
}

/* Modal Header */
.modal-header {

    padding: 10px;
    font-size: 18px;
    font-weight: bold;
}

/* Modal Body */
.modal-body {
    padding: 20px;
}

.modal-body img{
    max-width: 100%;
            height: auto;
            margin-bottom: 20px;
}

.modal-body img {
    max-width: 100%;
            height: auto;
            margin-bottom: 20px;
}

/* Input Styles */
.modal-body label {
    display: block;
            margin-bottom: 5px;
            font-size: 1rem;
            font-weight: bold;
            text-align: left;
}

.modal-body input {
    width: calc(100% - 20px);
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    box-sizing: border-box;
}

/* Buttons */
.modal-body button {
    display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            color: white;
            background-color: #28a745;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: -1rem;
}

.modal-body button:hover {
    background-color: #218838;
}

    </style>
    <body>

        <div class="header">
            <a href="customize-cart.php" class="back-link">
                <span class="back-arrow">&lt;</span> Back to Customize Cart
            </a>
        </div>
        <div class="container">
    <!-- Left Side: Cart/Items -->
    <div class="cart">
        <h3>Your Floral Arrangement Customization</h3>
            <?php foreach ($grouped_customization as $key => $group): ?>
                <?php
                // Fetch container details
                $stmt = $pdo->prepare("SELECT container_name FROM container WHERE container_id = :container_id");
                $stmt->execute(['container_id' => $group['container_type']]);
                $container = $stmt->fetch(PDO::FETCH_ASSOC);
                $container_name = $container['container_name'] ?? "Unknown Container";

                // Fetch color details
                $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = :color_id");
                $stmt->execute(['color_id' => $group['container_color']]);
                $color = $stmt->fetch(PDO::FETCH_ASSOC);
                $color_name = $color['color_name'] ?? "Unknown Color";

                // Get expected image or use a default placeholder
                $expected_image = $group['expected_image'] ?? '../images/default-placeholder.png';
                ?>
                <!-- HTML to display the details -->
                <div class="custom-item">
                    <h4>Container: <?php echo htmlspecialchars($container_name); ?></h4>
                    <p>Color: <?php echo htmlspecialchars($color_name); ?></p>
                    <img src="<?php echo htmlspecialchars($expected_image); ?>" alt="Expected Image" style="width: 200px; height: auto;" 
                        onerror="this.onerror=null;this.src='../images/default-placeholder.png';">
                </div>
            <?php endforeach; ?>
    </div>

            <!-- Right Side: Checkout -->
            <div class="payment">
                <h3>Checkout</h3>
                <form id="checkout-form" method="POST">
                    <label for="cust_name">Full Name:</label>
                    <span id="cust_name"><?php echo htmlspecialchars($customer['cust_name']); ?></span>

                    <label for="cust_email">Email:</label>
                    <span id="cust_email"><?php echo htmlspecialchars($customer['cust_email']); ?></span>

                    <label for="cust_address">Address:</label>
                    <span id="cust_address"><?php echo htmlspecialchars($customer['cust_address']); ?></span>

                    <label for="payment_method">Select Payment Method:</label>
                    <div class="pradio">
                        <input type="radio" name="payment_method" value="GCash" id="gcash" required>
                        <label for="gcash">
                        <img src="../images/Gcash.png" alt="GCash" width="50">
                            GCash
                        </label>
                    </div>
                    <div class="pradio">
                        <input type="radio" name="payment_method" value="Cash on Pickup" id="cop" required>
                        <label for="cop">
                        <img src="../images/cop.png" alt="Cash on PickUp" width="50">
                            Cash on Pickup
                        </label>
                    </div>
                </form>
                <hr>
                <div class="summary">
                    <p>
                        <strong>Total:</strong>
                        ₱<?php echo number_format($total_price, 2); ?>
                    </p>
                </div>
                <button class="checkout" id="proceed-payment">Proceed to Payment</button>
            </div>
        </div>

                <!-- GCash Modal -->
                <div class="modal" id="gcashModal">
                    <div class="modal-dialog">
                        <div class="modal-header">
                            GCash Payment
                            <button class="modal-close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Scan the QR Code to pay:</p>
                            <img src="../images/gcashqr.jpg" alt="GCash QR Code" style="width: 100%; height: auto;">
                            <form method="POST">
                                <input type="hidden" name="payment_method" value="GCash">
                                <label for="reference_number">Reference Number:</label>
                                <input type="text" name="reference_number" class="form-control" required>
                                <label for="amount_paid">Amount Paid:</label>
                                <input type="text" name="amount_paid" class="form-control" value="₱<?php echo number_format($total_price, 2); ?>" readonly>
                                <button type="submit" class="btn btn-success">Done</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cash on Pickup Modal -->
                <div class="modal" id="copModal">
                    <div class="modal-dialog">
                        <div class="modal-header">
                            Cash on Pickup
                            <button class="modal-close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Please bring the exact amount of ₱<?php echo number_format($total_price, 2); ?> upon pickup.</p>
                            <form method="POST">
                                <input type="hidden" name="payment_method" value="Cash on Pickup">
                                <input type="hidden" name="amount_paid" value="<?php echo $total_price; ?>">
                                <button type="submit" class="btn btn-success">Confirm</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>   
        </div>    
    </body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
