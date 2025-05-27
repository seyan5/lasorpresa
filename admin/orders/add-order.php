<?php 
session_start();
include_once('../conn.php');
require_once '../auth.php';

// Fetch active products for the product dropdown
$stmt = $pdo->prepare("SELECT p_id, name, current_price, quantity FROM product WHERE is_active = 1");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch customers to allow the admin to select
$stmt = $pdo->prepare("SELECT cust_id, cust_name FROM customer WHERE cust_status = 'active'");
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check for form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Customer information from the form
        $customer_id = $_POST['customer_id']; // Selected customer ID
        $full_name = $_POST['full_name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $postal_code = $_POST['postal_code'];
        $phone = $_POST['phone'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];
        $amount_paid = $_POST['amount_paid']; // Amount based on selected product and quantity

        // Check if the product is available in the required quantity
        $stmt = $pdo->prepare("SELECT quantity FROM product WHERE p_id = :product_id");
        $stmt->execute([':product_id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($product && $product['quantity'] >= $quantity) {
            // Reduce product quantity
            $new_quantity = $product['quantity'] - $quantity;
            $stmt = $pdo->prepare("UPDATE product SET quantity = :new_quantity WHERE p_id = :product_id");
            $stmt->execute([
                ':new_quantity' => $new_quantity,
                ':product_id' => $product_id
            ]);

            // Insert order details into the database
            $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total, full_name, address, city, postal_code, phone, created_at) 
                    VALUES (:customer_id, :total, :full_name, :address, :city, :postal_code, :phone, NOW())");
            $stmt->execute([
                ':customer_id' => $customer_id, // Use selected customer_id
                ':total' => $amount_paid,
                ':full_name' => $full_name,
                ':address' => $address,
                ':city' => $city,
                ':postal_code' => $postal_code,
                ':phone' => $phone,
            ]);

            $order_id = $pdo->lastInsertId();

            // Insert order items
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                    VALUES (:order_id, :product_id, :quantity, :price)");
            $stmt->execute([
                ':order_id' => $order_id,
                ':product_id' => $product_id,
                ':quantity' => $quantity,
                ':price' => $_POST['unit_price'],
            ]);

            // Insert payment details
            $stmt = $pdo->prepare("INSERT INTO payment (cust_id, order_id, cust_name, cust_email, reference_number, amount_paid, payment_method, payment_status, shipping_status, created_at) 
                    VALUES (:customer_id, :order_id, :cust_name, :cust_email, :reference_number, :amount_paid, 'cash', 'paid', 'delivered', NOW())");
            $stmt->execute([
                ':customer_id' => $customer_id, // Use selected customer_id
                ':order_id' => $order_id,
                ':cust_name' => $full_name,
                ':cust_email' => '', // Empty email for walk-in
                ':reference_number' => '0', // Walk-in doesn't have a reference number
                ':amount_paid' => $amount_paid,
            ]);

            // Send success response
            echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!']);
        } else {
            // Not enough stock
            echo json_encode(['status' => 'error', 'message' => 'Insufficient stock for this product.']);
        }
    } catch (Exception $e) {
        // Send error response
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Walk-In Order</title>
    <link rel="stylesheet" href="add-order.css">
    
    <!-- Include SweetAlert 2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <h1>Place Order for Walk-In Customer</h1>

        <!-- Update the form action to reflect the correct data submission -->
        <form action="add-order.php" method="POST">
            <!-- Existing form fields -->
            <label for="customer_id">Select Customer:</label>
            <select name="customer_id" id="customer_id" required>
                <option value="">Select Customer</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= $customer['cust_id'] ?>">
                        <?= htmlspecialchars($customer['cust_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" required><br>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" required><br>

            <label for="city">City:</label>
            <input type="text" id="city" name="city" required><br>

            <label for="postal_code">Postal Code:</label>
            <input type="text" id="postal_code" name="postal_code" required><br>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required><br>

            <label for="product_id">Product:</label>
            <select name="product_id" id="product_id" required>
                <option value="">Select Product</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['p_id'] ?>" data-price="<?= $product['current_price'] ?>" data-quantity="<?= $product['quantity'] ?>">
                        <?= htmlspecialchars($product['name']) ?> - ₱<?= number_format($product['current_price'], 2) ?>
                    </option>
                <?php endforeach; ?>
            </select><br>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" value="1" min="1" required><br>

            <label for="amount_paid">Amount Paid (₱):</label>
            <input type="text" id="amount_paid" name="amount_paid" readonly required><br>

            <!-- Hidden input to hold the price of the selected product -->
            <input type="hidden" id="unit_price" name="unit_price" value="">

            <button type="submit">Place Order</button>
        </form>
    </div>
    <script>
        document.getElementById("product_id").addEventListener("change", updateAmount);
        document.getElementById("quantity").addEventListener("input", updateAmount);

        function updateAmount() {
            const productSelect = document.getElementById("product_id");
            const quantityInput = document.getElementById("quantity");
            const amountPaidInput = document.getElementById("amount_paid");
            const unitPriceInput = document.getElementById("unit_price");

            const selectedOption = productSelect.options[productSelect.selectedIndex];
            if (selectedOption.value === "") return;  // If no product is selected, do nothing

            const price = parseFloat(selectedOption.getAttribute("data-price"));
            const quantity = parseInt(quantityInput.value);

            const amountPaid = price * quantity;
            amountPaidInput.value = amountPaid.toFixed(2);
            unitPriceInput.value = price; // Set unit price in the hidden input

            // Check if quantity is available in stock
            const availableStock = parseInt(selectedOption.getAttribute("data-quantity"));
            if (quantity > availableStock) {
                Swal.fire({
                    icon: 'error',
                    title: 'Stock Error',
                    text: 'Insufficient stock available!',
                });
                quantityInput.value = availableStock; // Reset to max available stock
            }
        }

        // Listen for form submission
        document.querySelector("form").addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent form from submitting the usual way

            // Collect the form data
            const formData = new FormData(this);

            // Send the form data using fetch
            fetch('add-order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Show success SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Order Added!',
                        text: data.message,
                    }).then(() => {
                        // Redirect to orders page after closing the alert
                        window.location.href = 'order.php';
                    });
                } else {
                    // Show error SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message,
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong!',
                });
            });
        });
    </script>
</body>
</html>
