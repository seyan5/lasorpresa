<?php
require 'header.php'; // Include any common setup, such as database connection

// Redirect to cart if the cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header('Location: shopcart.php');
    exit;
}

$total = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart']));
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/shopcart.css">
    <link rel="stylesheet" href="../css/checkout.css">
    <title>Checkout</title>
    <script>
        function toggleGCashModal(show) {
            const modal = document.getElementById('gcash-modal');
            modal.style.display = show ? 'block' : 'none';
        }

        function validateForm() {
            const fullName = document.getElementById('full_name').value.trim();
            const address = document.getElementById('address').value.trim();
            const city = document.getElementById('city').value.trim();
            const postalCode = document.getElementById('postal_code').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

            // Check if all fields are filled and a payment method is selected
            const isFormValid = fullName && address && city && postalCode && phone && paymentMethod;

            // Enable or disable the checkout button
            const checkoutButton = document.querySelector('.checkout');
            checkoutButton.disabled = !isFormValid;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const formFields = document.querySelectorAll('#checkout-form input, #checkout-form textarea, #checkout-form input[name="payment_method"]');
            formFields.forEach(field => {
                field.addEventListener('input', validateForm);
            });
        });

        function handleCheckout() {
            const fullName = document.getElementById('full_name').value.trim();
            const address = document.getElementById('address').value.trim();
            const city = document.getElementById('city').value.trim();
            const postalCode = document.getElementById('postal_code').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

            // Check if all required fields are filled
            if (!fullName || !address || !city || !postalCode || !phone || !paymentMethod) {
                alert('Please fill out all the required fields and select a payment method before proceeding.');
                return;
            }

            // Check selected payment method and proceed accordingly
            if (paymentMethod.value === 'gcash') {
                toggleGCashModal(true);
            } else if (paymentMethod.value === 'cod') {
                window.location.href = 'order_submitted.php';
            }
        }


        function handleGCash() {
            const referenceNo = document.getElementById('reference_no').value;
            const amountPaid = document.getElementById('amount_paid').value;
            const datePaid = document.getElementById('date_paid').value;
            const timePaid = document.getElementById('time_paid').value;

            if (!referenceNo || !amountPaid || !datePaid || !timePaid) {
                alert('Please fill out all GCash details.');
                return;
            }

            // Redirect after successful input (can be enhanced with AJAX for smoother experience)
            window.location.href = 'order_submitted.php';
        }

        window.onclick = function (event) {
            const modal = document.getElementById('gcash-modal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
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
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .modal img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <a href="index.php" class="back-link">
            <span class="back-arrow">&lt;</span> La Sorpresa Home Page
        </a>
        <a href="shopcart.php" class="back-link">
            <span class="back-arrow">&lt;</span> Back to Cart
        </a>
    </div>

    <div class="container">
        <div class="cart">
            <hr>
            <h3>Order Summary</h3>

            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <p>You have <?php echo count($_SESSION['cart']); ?> items in your cart</p>

                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="cart-item">
                        <?php if (isset($item['image']) && $item['image']): ?>
                            <img src="../admin/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                alt="<?php echo htmlspecialchars($item['name']); ?>" width="50">
                        <?php else: ?>
                            <img src="path/to/default-image.jpg" alt="No image available" width="50">
                        <?php endif; ?>

                        <div>
                            <p><?php echo htmlspecialchars($item['name']); ?></p>
                            <p><?php echo htmlspecialchars($item['quantity']); ?> pcs.</p>
                        </div>

                        <div class="quantity">
                            <?php echo $item['quantity']; ?>
                        </div>

                        <div class="price">
                            ₱<?php echo number_format($item['price'], 2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
        <div class="payment">
            <h3>Shipping Information</h3>
            <form id="checkout-form">
                <label for="full_name">Full Name: </label>
                <input type="text" id="full_name" name="full_name" required>

                <label for="address">Address: </label>
                <textarea id="address" name="address" rows="3" required></textarea>

                <label for="city">City: </label>
                <input type="text" id="city" name="city" required>

                <label for="postal_code">Postal Code: </label>
                <input type="text" id="postal_code" name="postal_code" required>

                <label for="phone">Phone Number: </label>
                <input type="text" id="phone" name="phone" required>

                <label for="payment_method">Mode of Payment:</label>
                <div>
                    <input type="radio" id="gcash" name="payment_method" value="gcash" required>
                    <label for="gcash">
                        <img src="../images/Gcash.png" alt="GCash" width="50">
                        GCash
                    </label>
                </div>
                <div>
                    <input type="radio" id="cod" name="payment_method" value="cod" required>
                    <label for="cod">
                        <img src="../images/cod.png" alt="Cash on Delivery" width="50">
                        Cash on Delivery (COD)
                    </label>
                </div>
            </form>

            <hr>

            <div class="summary">
                <p>Subtotal <span>₱<?php
                $subtotal = array_sum(array_map(function ($item) {
                    return $item['price'] * $item['quantity'];
                }, $_SESSION['cart']));
                echo number_format($subtotal, 2);
                ?></span></p>
                <p>Shipping <span>₱0</span></p>
                <p>
                    <strong>Total:</strong>
                    ₱<?php
                    echo number_format($total, 2);
                    ?>
                </p>
            </div>
            <button class="checkout" type="button" onclick="handleCheckout()" disabled>Checkout</button>
        </div>
    </div>

    <!-- GCash Modal -->
    <div id="gcash-modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleGCashModal(false)">&times</span>
            <h3>Scan to Pay</h3>
            <img src="../images/gcashqr.jpg" alt="GCash QR Code">
            <h3>GCash Payment Details</h3>
            <label for="reference_no">Reference Number: </label>
            <input type="text" id="reference_no" name="reference_no">

            <label for="amount_paid">Amount Paid: </label>
            <input type="number" id="amount_paid" name="amount_paid">

            <label for="date_paid">Date: </label>
            <input type="date" id="date_paid" name="date_paid">

            <label for="time_paid">Time: </label>
            <input type="time" id="time_paid" name="time_paid">
            <button class="checkout" type="button" onclick="handleGCash()">Done</button>
        </div>
    </div>
</body>

</html>