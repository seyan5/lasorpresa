<?php
session_start();
require_once('db_connection.php'); // Include DB connection

if (!isset($_SESSION['customization'])) {
    echo "No customization found. Please go back and customize your arrangement.";
    exit;
}

$customization = $_SESSION['customization']; // Retrieve customization data from session

// Handle form submission for order confirmation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Customer details
    $customer_name = $_POST['customer_name'];
    $customer_email = $_POST['customer_email'];
    $shipping_address = $_POST['shipping_address'];

    // Start transaction
    try {
        $pdo->beginTransaction(); // Begin transaction for multiple queries

        // Insert the order into the orders table
        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, shipping_address) VALUES (:customer_name, :customer_email, :shipping_address)");
        $stmt->execute([
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'shipping_address' => $shipping_address
        ]);

        $order_id = $pdo->lastInsertId(); // Get the inserted order ID

        $total_price = 0; // Initialize the total price

        // Insert the customization details into the order_items table
        foreach ($customization as $item) {
            // Fetch flower price
            $stmt = $pdo->prepare("SELECT price FROM flowers WHERE flower_type = :flower_type");
            $stmt->execute(['flower_type' => $item['flower_type']]);
            $flower_price = $stmt->fetchColumn();

            // Fetch container price
            $stmt = $pdo->prepare("SELECT price FROM containers WHERE container_type = :container_type");
            $stmt->execute(['container_type' => $item['container_type']]);
            $container_price = $stmt->fetchColumn();

            // Fetch container color price (optional)
            $stmt = $pdo->prepare("SELECT price FROM colors WHERE color_name = :color_name");
            $stmt->execute(['color_name' => $item['container_color']]);
            $color_price = $stmt->fetchColumn();

            // Calculate price for this item
            $item_total_price = ($flower_price * $item['num_flowers']) + $container_price + $color_price;
            $total_price += $item_total_price; // Add to total price

            // Insert the item into order_items table
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, flower_type, num_flowers, container_type, container_color, price) 
                                   VALUES (:order_id, :flower_type, :num_flowers, :container_type, :container_color, :price)");
            $stmt->execute([
                'order_id' => $order_id,
                'flower_type' => $item['flower_type'],
                'num_flowers' => $item['num_flowers'],
                'container_type' => $item['container_type'],
                'container_color' => $item['container_color'],
                'price' => $item_total_price
            ]);
        }

        // Commit transaction
        $pdo->commit();

        // Clear the customization session data after successful order placement
        unset($_SESSION['customization']);

        // Redirect to order confirmation page
        header("Location: order-confirmation.php?order_id=" . $order_id);
        exit;

    } catch (PDOException $e) {
        // Rollback if there's any error
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

?>

<h3>Your Floral Arrangement Customization</h3>

<div class="customization-summary">
    <?php 
    $total_price = 0; // Initialize total price for display
    foreach ($customization as $index => $item): 
        // Fetch flower price
        $stmt = $pdo->prepare("SELECT price FROM flowers WHERE flower_type = :flower_type");
        $stmt->execute(['flower_type' => $item['flower_type']]);
        $flower_price = $stmt->fetchColumn();

        // Fetch container price
        $stmt = $pdo->prepare("SELECT price FROM containers WHERE container_type = :container_type");
        $stmt->execute(['container_type' => $item['container_type']]);
        $container_price = $stmt->fetchColumn();

        // Fetch color price
        $stmt = $pdo->prepare("SELECT price FROM colors WHERE color_name = :color_name");
        $stmt->execute(['color_name' => $item['container_color']]);
        $color_price = $stmt->fetchColumn();

        // Calculate total price for this flower set
        $item_total_price = ($flower_price * $item['num_flowers']) + $container_price + $color_price;
        $total_price += $item_total_price;
    ?>
        <div class="customization-item">
            <h4>Flower Set <?php echo $index + 1; ?>:</h4>
            <p><strong>Flower Type:</strong> <?php echo htmlspecialchars($item['flower_type']); ?> ($<?php echo $flower_price; ?> per flower)</p>
            <p><strong>Number of Flowers:</strong> <?php echo htmlspecialchars($item['num_flowers']); ?></p>
            <p><strong>Container Type:</strong> <?php echo htmlspecialchars($item['container_type']); ?> ($<?php echo $container_price; ?>)</p>
            <p><strong>Container Color:</strong> <?php echo htmlspecialchars($item['container_color']); ?> ($<?php echo $color_price; ?>)</p>
            <p><strong>Item Total Price:</strong> $<?php echo number_format($item_total_price, 2); ?></p>
        </div>
        <hr>
    <?php endforeach; ?>

    <!-- Total Price -->
    <p><strong>Total Price:</strong> $<?php echo number_format($total_price, 2); ?></p>

    <!-- Checkout Form -->
    <form action="checkout.php" method="POST">
        <label for="customer_name">Name:</label>
        <input type="text" name="customer_name" required><br>

        <label for="customer_email">Email:</label>
        <input type="email" name="customer_email" required><br>

        <label for="shipping_address">Shipping Address:</label>
        <textarea name="shipping_address" required></textarea><br>

        <button type="submit" class="btn btn-primary">Confirm Purchase</button>
    </form>
</div>
