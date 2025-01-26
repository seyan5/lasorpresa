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
            'expected_image' => $expected_image,
        ];
    }
}

// Check if valid customizations exist
if (empty($grouped_customization)) {
    echo "<p>Your cart is empty.</p>";
    $total_price = 0; // Ensure total_price is set to 0
}
?>
<?php include('navuser.php'); ?>
<?php include('back.php'); ?>
<link rel="stylesheet" href="../css/shopcart.css">

<body>
    <div class="container">
        <div class="cart">
            <hr>
            <h3>Order Summary</h3>

            <?php if (!empty($grouped_customization)): ?>
                <p>You have <?php echo count($grouped_customization); ?> items in your cart</p>

                <?php foreach ($grouped_customization as $customization): ?>
                    <div class="cart-item">
                        <?php if (!empty($customization['expected_image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($customization['expected_image']); ?>" alt="<?php echo htmlspecialchars($customization['container_name']); ?>" width="50">
                        <?php else: ?>
                            <img src="path/to/default-image.jpg" alt="No image available" width="50">
                        <?php endif; ?>

                        <div>
                            <p>Container Type: <?php echo htmlspecialchars($customization['container_name']); ?> (₱<?php echo number_format($customization['container_price'], 2); ?>)</p>
                            <p>Flowers:
                                <?php foreach ($customization['flower_details'] as $flower): ?>
                                    <?php echo htmlspecialchars($flower); ?><br>
                                <?php endforeach; ?>
                            </p>
                            <p>Remarks: <?php echo htmlspecialchars($customization['remarks']); ?></p>
                        </div>

                        <div class="price">
                            ₱<?php echo number_format($customization['total_price'] ?? 0, 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <div class="payment">
            <h3>Payment</h3>
            <form id="checkout-form" action="customize-checkout.php" method="POST">
                <label for="cust_name">Full Name: </label>
                <span id="cust_name"><?php echo htmlspecialchars($customer['cust_name'] ?? ''); ?></span>

                <label for="cust_phone">Phone Number: </label>
                <span id="cust_phone"><?php echo htmlspecialchars($customer['cust_phone'] ?? ''); ?></span>

                <label for="address">Address: </label>
                <span id="cust_address"><?php echo htmlspecialchars($customer['cust_address'] ?? ''); ?></span>

                <input type="hidden" name="selected_customizations" value="<?php echo htmlspecialchars(json_encode($selected_indices)); ?>">
                <label for="payment_method">Mode of Payment:</label>
                <div class="pradio">
                    <input type="radio" id="gcash" name="payment_method" value="gcash" required>
                    <label for="gcash">
                        <img src="../images/Gcash.png" alt="GCash" width="50">
                        GCash
                    </label>
                </div>
                <div class="pradio">
                    <input type="radio" id="cop" name="payment_method" value="cop" required>
                    <label for="cop">
                        <img src="../images/cop.png" alt="Cash on PickUp" width="50">
                        Cash on Pickup
                    </label>
                </div>
            </form>

            <hr>
            <div class="summary">
                <p>Subtotal <span>₱<?php echo number_format($total_price ?? 0, 2); ?></span></p>
                <p><strong>Total:</strong> ₱<?php echo number_format($total_price ?? 0, 2); ?></p>
            </div>
            <button class="checkout" type="button" onclick="handleCheckout()" disabled>Checkout</button>
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
</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const formFields = document.querySelectorAll('#checkout-form input, #checkout-form textarea, #checkout-form input[name="payment_method"]');
        formFields.forEach(field => {
            field.addEventListener('input', validateForm);
        });
    });

    function validateForm() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        const checkoutButton = document.querySelector('.checkout');
        checkoutButton.disabled = !paymentMethod;
    }

    function handleCheckout() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            alert('Please select a payment method.');
            return;
        }
        if (paymentMethod.value === 'gcash') {
            document.getElementById('gcashModal').style.display = 'block';
        } else if (paymentMethod.value === 'cop') {
            document.getElementById('copModal').style.display = 'block';
        }
    }

    window.onclick = function (event) {
        if (event.target === document.getElementById('gcashModal')) {
            document.getElementById('gcashModal').style.display = 'none';
        }
        if (event.target === document.getElementById('copModal')) {
            document.getElementById('copModal').style.display = 'none';
        }
    };
</script>

<style>
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
    .modal-close {
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #aaa;
    }
    .modal-close:hover,
    .modal-close:focus {
        color: black;
    }
    .modal img {
        max-width: 100%;
        height: auto;
    }
    .modal-content label {
        display: block;
        margin-bottom: 5px;
        font-size: 1rem;
        font-weight: bold;
        text-align: left;
    }
    .modal-content input {
        width: calc(100% - 20px);
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
        box-sizing: border-box;
    }
    .modal-content button {
        display: inline-block;
        padding: 10px 20px;
        font-size: 1rem;
        color: white;
        background-color: #28a745;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .modal-content button:hover {
        background-color: #218838;
    }
</style>
