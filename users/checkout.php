<?php
include("conn.php");

// Ensure user is logged in
if (!isset($_SESSION['customer'])) {
    echo "<script>
            alert('You need to be logged in to access this page!');
            window.location.href = 'login.php';
          </script>";
    exit;
}



// Fetch user details
$cust_email = $_SESSION['customer']['cust_email'] ?? null;
$cust_id = $_SESSION['customer']['cust_id'] ?? null;

try {
    $stmt = $pdo->prepare("SELECT cust_name, cust_phone, cust_address, cust_city, cust_zip FROM customer WHERE cust_email = :cust_email");
    $stmt->execute([':cust_email' => $cust_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("User not found. Please log in again.");
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Filter the cart items based on selected_items
$selected_items = $_POST['selected_items'] ?? [];
if (empty($selected_items)) {
    die("No items selected for checkout.");
}

$selected_cart_items = [];
$total = 0;

// Filter selected items
foreach ($selected_items as $index) {
    if (isset($_SESSION['cart'][$index])) {
        $selected_cart_items[$index] = $_SESSION['cart'][$index];
        $total += $_SESSION['cart'][$index]['price'] * $_SESSION['cart'][$index]['quantity'];
    }
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

            <?php if (!empty($selected_cart_items)): ?>
                <p>You have <?php echo count($selected_cart_items); ?> items in your order.</p>

                <?php foreach ($selected_cart_items as $index => $item): ?>
                    <div class="cart-item">
                        <img src="../admin/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                            alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
                        <p><?php echo htmlspecialchars($item['name']); ?></p>
                        <p><?php echo htmlspecialchars($item['quantity']); ?> pcs.</p>
                        <div class="price">
                            ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty or no items were selected.</p>
            <?php endif; ?>
        </div>

        <div class="payment">
            <h3>Shipping Information</h3>
            <form id="checkout-form">
                <input type="hidden" name="selected_items"
                    value="<?php echo implode(',', array_keys($selected_cart_items)); ?>">

                <label for="cust_name">Full Name: </label>
                <span id="cust_name"><?php echo htmlspecialchars($user['cust_name']); ?></span>

                <label for="cust_phone">Phone Number: </label>
                <span id="cust_phone"><?php echo htmlspecialchars($user['cust_phone']); ?></span>

                <label for="address">Address: </label>
                <span id="cust_address"><?php echo htmlspecialchars($user['cust_address']); ?></span>

                <label for="payment_method">Mode of Payment:</label>
                <div class="pradio">
                    <input type="radio" id="gcash" name="payment_method" value="gcash" required>
                    <label for="gcash">
                        <img src="../images/Gcash.png" alt="GCash" width="50"> GCash
                    </label>
                </div>
                <div class="pradio">
                    <input type="radio" id="cop" name="payment_method" value="cop" required>
                    <label for="cop">
                        <img src="../images/cop.png" alt="Cash on Pickup" width="50"> Cash on Pickup
                    </label>
                </div>
            </form>

            <hr>
            <div class="summary">
                <p>Subtotal <span>₱<?php echo number_format($total, 2); ?></span></p>
                <p>
                    <strong>Total:</strong>
                    ₱<?php echo number_format($total, 2); ?>
                </p>
            </div>
            <button class="checkout" type="button" onclick="handleCheckout()">Checkout</button>
        </div>
    </div>

    <!-- GCash Modal -->
    <div id="gcash-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleGCashModal(false)">&times;</span>
            <h3>Scan to Pay</h3>
            <img src="../images/gcashqr.jpg" alt="GCash QR Code">
            <label for="reference_number">Reference Number: </label>
            <input type="text" id="reference_number" name="reference_no">
            <label for="amount_paid">Amount Paid: </label>
            <input type="number" id="amount_paid" name="amount_paid" readonly>
            <button class="checkout" type="button" onclick="handleGCash()">Done</button>
        </div>
    </div>

    <!-- COP Modal -->
    <div id="cop-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleCOPModal(false)">&times;</span>
            <h3>Confirm Cash on Pickup</h3>
            <p>Your total is ₱<span id="cop-total"></span>. Do you want to proceed?</p>
            <button class="checkout" type="button" onclick="handleCOP()">Confirm</button>
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    function handleCheckout() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            alert('Please select a payment method.');
            return;
        }

        if (paymentMethod.value === 'gcash') {
            toggleGCashModal(true);
        } else if (paymentMethod.value === 'cop') {
            toggleCOPModal(true);
        }
    }

    function handleGCash() {
        const referenceNumber = document.getElementById('reference_number').value.trim();
        const amountPaid = document.getElementById('amount_paid').value.trim();

        if (!referenceNumber || !amountPaid) {
            alert('Please fill out all GCash details.');
            return;
        }

        processCheckout('gcash', referenceNumber, amountPaid);
    }

    function handleCOP() {
        const total = <?php echo $total; ?>;
        processCheckout('cop', '0', total);
    }

    function processCheckout(paymentMethod, referenceNumber, amountPaid) {
        fetch('checkout-handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                payment_method: paymentMethod,
                reference_number: referenceNumber,
                amount_paid: amountPaid,
                selected_items: '<?php echo implode(",", array_keys($selected_cart_items)); ?>'
            }),
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.href = 'customer-order.php';
                } else {
                    alert(data.message || 'An error occurred.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('An unexpected error occurred.');
            });
    }

    function toggleGCashModal(show) {
        const modal = document.getElementById('gcash-modal');
        const total = <?php echo $total; ?>;
        const amountPaidInput = document.getElementById('amount_paid');

        if (show) {
            amountPaidInput.value = total.toFixed(2);
        }

        modal.style.display = show ? 'block' : 'none';
    }

    function toggleCOPModal(show) {
        const modal = document.getElementById('cop-modal');
        const total = <?php echo $total; ?>;
        document.getElementById('cop-total').textContent = total.toFixed(2);
        modal.style.display = show ? 'block' : 'none';
    }
</script>

<style>
    /* Modal styles */
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

    .modal-content {
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

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
    }

    .modal img {
        max-width: 100%;
        height: auto;
        margin-bottom: 20px;
    }

    /* Input field styles */
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

</html>