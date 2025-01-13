<?php
ob_start();
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: login.php');
    exit;
}

// Fetch the logged-in user's details
$cust_email = $_SESSION['customer']['cust_email'] ?? null;
$cust_id = $_SESSION['customer']['cust_id'] ?? null;

// Debugging: Check session data and cust_id
if (!$cust_id) {
    file_put_contents('debug.log', "Error: Customer ID not found in session.\n", FILE_APPEND);
    die("Error: Customer ID not found in session.");
}
file_put_contents('debug.log', "Session Data: " . print_r($_SESSION, true), FILE_APPEND);

// Fetch user details
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

// Calculate the total cart value
$total = array_sum(array_map(function ($item) {
    return $item['price'] * $item['quantity'];
}, $_SESSION['cart'] ?? []));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['customer']) || empty($_SESSION['cart'])) {
        echo json_encode(['status' => 'error', 'message' => 'Cart is empty or user not logged in']);
        exit;
    }

    $payment_method = $_POST['payment_method'] ?? '';
    $reference_number = $payment_method === 'cop' ? '0' : ($_POST['reference_number'] ?? null);
    $amount_paid = $payment_method === 'cop' ? $total : ($_POST['amount_paid'] ?? null);
    $shipping_status = 'pending';
    $payment_status = $payment_method === 'gcash' ? 'paid' : 'pending';

    // Validate payment method
    if (empty($payment_method)) {
        echo json_encode(['status' => 'error', 'message' => 'Payment method is required']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // Insert the order
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total, full_name, address, city, postal_code, phone, created_at) 
            VALUES (:customer_id, :total, :full_name, :address, :city, :postal_code, :phone, NOW())");
        $stmt->execute([
            ':customer_id' => $cust_id,
            ':total' => $total,
            ':full_name' => $user['cust_name'],
            ':address' => $user['cust_address'],
            ':city' => $user['cust_city'],
            ':postal_code' => $user['cust_zip'],
            ':phone' => $user['cust_phone']
        ]);

        // Get the order ID
        $order_id = $pdo->lastInsertId();

        foreach ($_SESSION['cart'] as $product_id => $item) {
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (:order_id, :product_id, :quantity, :price)");
            $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':quantity' => $item['quantity'],
                ':price' => $item['price']
            ]);

            $stmt = $pdo->prepare("
                UPDATE product
                SET quantity = quantity - :quantity
                WHERE p_id = :product_id AND quantity >= :quantity
            ");
            $stmt->execute([
                ':quantity' => $item['quantity'],
                ':product_id' => $product_id
            ]);
        }

        // Insert payment
        $stmt = $pdo->prepare("INSERT INTO payment (cust_id, order_id, cust_name, cust_email, reference_number, amount_paid, payment_method, payment_status, shipping_status)
            VALUES (:cust_id, :order_id, :cust_name, :cust_email, :reference_number, :amount_paid, :payment_method, :payment_status, :shipping_status)");
        $stmt->execute([
            ':cust_id' => $cust_id,
            ':order_id' => $order_id,
            ':cust_name' => $user['cust_name'],
            ':cust_email' => $cust_email,
            ':reference_number' => $reference_number,
            ':amount_paid' => $amount_paid,
            ':payment_method' => $payment_method, // Ensure this value is set
            ':payment_status' => $payment_status,
            ':shipping_status' => $shipping_status
        ]);

        // Debugging: Log payment success
        file_put_contents('debug.log', "Payment inserted: Method - $payment_method\n", FILE_APPEND);

        $pdo->commit();
        unset($_SESSION['cart']);

        echo json_encode(['status' => 'success', 'message' => 'Order registered successfully']);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit;
}

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- css -->
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/shopcart.css?v1.1">
    <link rel="stylesheet" href="../css/checkout.css">
</head>

<body>
    <!-- header -->

    <header>

        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>

        <!-- <a href="#" class="logo">Flower<span>.</span></a> -->
        <img src="../images/logo.png" alt="" class="logos" href="">
        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="#about">About</a>
            <div class="prod-dropdown">
                <a href="" onclick="toggleDropdown()">Products</a>
                <div class="prod-menu" id="prodDropdown">
                    <a href="products.php">Flowers</a>
                    <a href="occasion.php">Occasion</a>
                    <a href="addons.php">Addons</a>
                </div>
            </div>
            <a href="#review">Review</a>
            <a href="#contacts">Contacts</a>
            <a href="customization.php">Customize</a>

        </nav>

        <div class="icons">
            <a href="shopcart.php" class="fas fa-shopping-cart"></a>
            <div class="user-dropdown">
                <a href="#" class="fas fa-user" onclick="toggleDropdown()"></a>
                <div class="dropdown-menu" id="userDropdown">
                    <?php if (isset($_SESSION['customer'])): ?>
                        <p>Welcome, <?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?></p>
                        <hr>
                        <a href="customer-profile-update.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </header>

    <body>
        <div class="header">
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
                    <label for="cust_name">Full Name: </label>
                    <span id="cust_name"><?php echo htmlspecialchars($user['cust_name']); ?></span>

                    <label for="cust_phone">Phone Number: </label>
                    <span id="cust_phone"><?php echo htmlspecialchars($user['cust_phone']); ?></span>

                    <label for="address">Address: </label>
                    <span id="cust_address"><?php echo htmlspecialchars($user['cust_address']); ?></span>


                    <label for="payment_method">Mode of Payment:</label>
                    <div>
                        <input type="radio" id="gcash" name="payment_method" value="gcash" required>
                        <label for="gcash">
                            <img src="../images/Gcash.png" alt="GCash" width="50">
                            GCash
                        </label>
                    </div>
                    <div>
                        <input type="radio" id="cop" name="payment_method" value="cop" required>
                        <label for="cop">
                            <img src="../images/cop.png" alt="Cash on PickUp" width="50">
                            Cash on Pickup
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
                <label for="reference_number">Reference Number: </label>
                <input type="text" id="reference_number" name="reference_no">

                <label for="amount_paid">Amount Paid: </label>
                <input type="number" id="amount_paid" name="amount_paid">
                <button class="checkout" type="button" onclick="handleGCash()">Done</button>
            </div>
        </div>

        <!-- COP Modal -->
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


    <script>

        function handleGCash() {
            const referenceNumber = document.getElementById('reference_number').value.trim();
            const amountPaid = document.getElementById('amount_paid').value.trim();

            if (!referenceNumber || !amountPaid) {
                alert('Please fill out all GCash details.');
                return;
            }

            // Send POST request for GCash
            fetch('checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    payment_method: 'gcash', // Set payment method as GCash
                    reference_number: referenceNumber,
                    amount_paid: amountPaid,
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.href = 'shopcart.php'; // Redirect to cart or confirmation page
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
            modal.style.display = show ? 'block' : 'none';
        }


        document.addEventListener('DOMContentLoaded', () => {
            const formFields = document.querySelectorAll('#checkout-form input, #checkout-form textarea, #checkout-form input[name="payment_method"]');
            formFields.forEach(field => {
                field.addEventListener('input', validateForm);
            });
        });

        function validateForm() {
            // Get the payment method selection
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked');

            // Enable or disable the checkout button based on the payment method selection
            const checkoutButton = document.querySelector('.checkout');
            checkoutButton.disabled = !paymentMethod;
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Add event listener to payment method inputs
            const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
            paymentMethods.forEach(method => {
                method.addEventListener('change', validateForm);
            });

            // Initial validation
            validateForm();
        });

        function toggleCOPModal(show) {
            const modal = document.getElementById('cop-modal');
            const total = <?php echo $total; ?>; // Pass total amount from PHP
            document.getElementById('cop-total').textContent = total.toFixed(2);
            modal.style.display = show ? 'block' : 'none';
        }

        function handleCOP() {
            const total = <?php echo $total; ?>;

            // Send POST request for Cash on Pickup
            fetch('checkout.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    payment_method: 'cop', // Ensure payment method is set
                    reference_number: '0', // Default for COP
                    amount_paid: total,    // Set total cart value
                }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.href = 'shopcart.php'; // Redirect to cart or confirmation page
                    } else {
                        alert(data.message || 'An error occurred.');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An unexpected error occurred.');
                });
        }


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

        window.onclick = function (event) {
            const gcashModal = document.getElementById('gcash-modal');
            const copModal = document.getElementById('cop-modal');
            if (event.target === gcashModal) {
                gcashModal.style.display = 'none';
            }
            if (event.target === copModal) {
                copModal.style.display = 'none';
            }
        };




        window.onclick = function (event) {
            const modal = document.getElementById('gcash-modal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }

        }

        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }

        // Close the dropdown when clicking outside
        window.onclick = function (event) {
            if (!event.target.matches('.fa-user')) {
                const dropdown = document.getElementById('userDropdown');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        };

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