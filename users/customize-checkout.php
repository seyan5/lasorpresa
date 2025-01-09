<?php
require_once('header.php'); // Include DB connection

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

        // Insert the order into the custom_order table
        $stmt = $pdo->prepare("INSERT INTO custom_order (customer_name, customer_email, shipping_address, total_price) VALUES (:customer_name, :customer_email, :shipping_address, :total_price)");
        $stmt->execute([
            'customer_name' => $customer_name,
            'customer_email' => $customer_email,
            'shipping_address' => $shipping_address,
            'total_price' => 0 // This will be updated after inserting order items
        ]);

        $order_id = $pdo->lastInsertId(); // Get the inserted order ID
        $total_price = 0; // Initialize the total price

        // Insert the customization details into the custom_orderitems table
        foreach ($customization as $item) {
            // Fetch flower price
            $stmt = $pdo->prepare("SELECT price FROM flowers WHERE name = :flower_type"); // 'name' instead of 'flower_type'
            $stmt->execute(['flower_type' => $item['flower_type']]);
            $flower_price = $stmt->fetchColumn();

            // Fetch container price
            $stmt = $pdo->prepare("SELECT price FROM container WHERE container_name = :container_type"); // 'container_name' instead of 'container_type'
            $stmt->execute(['container_type' => $item['container_type']]);
            $container_price = $stmt->fetchColumn();

            // No need to fetch color price if there's no price for color
            $color_price = 0; // Set color price to 0

            // Debugging: Display the fetched prices
echo "Flower Price: " . $flower_price . "<br>";
echo "Container Price: " . $container_price . "<br>";
echo "Color Price: " . $color_price . "<br>";

            // Calculate price for this item (no color price involved)
            $item_total_price = ($flower_price * $item['num_flowers']) + $container_price + $color_price;
            $total_price += $item_total_price; // Add to total price

            // Insert the item into custom_orderitems table
            $stmt = $pdo->prepare("INSERT INTO custom_orderitems (order_id, flower_type, num_flowers, container_type, container_color, flower_price, container_price, color_price, total_price) 
                                   VALUES (:order_id, :flower_type, :num_flowers, :container_type, :container_color, :flower_price, :container_price, :color_price, :total_price)");
            $stmt->execute([
                'order_id' => $order_id,
                'flower_type' => $item['flower_type'],
                'num_flowers' => $item['num_flowers'],
                'container_type' => $item['container_type'],
                'container_color' => $item['container_color'],
                'flower_price' => $flower_price,
                'container_price' => $container_price,
                'color_price' => $color_price, // This will be 0
                'total_price' => $item_total_price
            ]);
        }

        // Update the total price in the custom_order table
        $stmt = $pdo->prepare("UPDATE custom_order SET total_price = :total_price WHERE order_id = :order_id");
        $stmt->execute([
            'total_price' => $total_price,
            'order_id' => $order_id
        ]);

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
        $stmt = $pdo->prepare("SELECT price FROM flowers WHERE name = :flower_type");
        $stmt->execute(['flower_type' => $item['flower_type']]);
        $flower_price = $stmt->fetchColumn();

        // Fetch container price
        $stmt = $pdo->prepare("SELECT price FROM container WHERE container_name = :container_type");
        $stmt->execute(['container_type' => $item['container_type']]);
        $container_price = $stmt->fetchColumn();

        // No need to fetch color price
        $color_price = 0; // Color price is 0

        // Calculate total price for this flower set
        $item_total_price = ($flower_price * $item['num_flowers']) + $container_price + $color_price;
        $total_price += $item_total_price;
    ?>
        <div class="customization-item">
            <h4>Flower Set <?php echo $index + 1; ?>:</h4>
            <p><strong>Flower Type:</strong> <?php echo htmlspecialchars($item['flower_type']); ?> ($<?php echo $flower_price; ?> per flower)</p>
            <p><strong>Number of Flowers:</strong> <?php echo htmlspecialchars($item['num_flowers']); ?></p>
            <p><strong>Container Type:</strong> <?php echo htmlspecialchars($item['container_type']); ?> ($<?php echo $container_price; ?>)</p>
            <p><strong>Container Color:</strong> <?php echo htmlspecialchars($item['container_color']); ?> (No extra cost)</p>
            <p><strong>Item Total Price:</strong> $<?php echo number_format($item_total_price, 2); ?></p>
        </div>
        <hr>
    <?php endforeach; ?>

    <!-- Total Price -->
    <p><strong>Total Price:</strong> $<?php echo number_format($total_price, 2); ?></p>

    <!-- Checkout Form -->
    <form action="customize-checkout.php" method="POST">
        <label for="customer_name">Name:</label>
        <input type="text" name="customer_name" required><br>

        <label for="customer_email">Email:</label>
        <input type="email" name="customer_email" required><br>

        <label for="shipping_address">Shipping Address:</label>
        <textarea name="shipping_address" required></textarea><br>

        <button type="submit" class="btn btn-primary">Confirm Purchase</button>
    </form>
</div>
