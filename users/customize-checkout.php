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
            // Fetch flower details (price and name)
            $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE name = :flower_type");
            $stmt->execute(['flower_type' => $item['flower_type']]);
            $flower = $stmt->fetch(PDO::FETCH_ASSOC);
            $flower_name = $flower['name'];
            $flower_price = $flower['price'];

            // Fetch container details (name and price)
            $stmt = $pdo->prepare("SELECT container_name, price FROM container WHERE container_name = :container_type");
            $stmt->execute(['container_type' => $item['container_type']]);
            $container = $stmt->fetch(PDO::FETCH_ASSOC);
            $container_name = $container['container_name'];
            $container_price = $container['price'];

            // Fetch color details (name, no price)
            $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_name = :color_name");
            $stmt->execute(['color_name' => $item['container_color']]);
            $color = $stmt->fetch(PDO::FETCH_ASSOC);
            $color_name = $color['color_name'];
            $color_price = 0; // No extra cost for color

            // Calculate price for this item
            $item_total_price = ($flower_price * $item['num_flowers']) + $container_price + $color_price;
            $total_price += $item_total_price; // Add to total price

            // Insert the item into custom_orderitems table
            $stmt = $pdo->prepare("INSERT INTO custom_orderitems (order_id, flower_type, num_flowers, container_type, container_color, flower_price, container_price, color_price, total_price) 
                                   VALUES (:order_id, :flower_type, :num_flowers, :container_type, :container_color, :flower_price, :container_price, :color_price, :total_price)");
            $stmt->execute([
                'order_id' => $order_id,
                'flower_type' => $flower_name, // Store flower name
                'num_flowers' => $item['num_flowers'],
                'container_type' => $container_name, // Store container name
                'container_color' => $color_name, // Store color name
                'flower_price' => $flower_price,
                'container_price' => $container_price,
                'color_price' => $color_price,
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
        // Fetch flower details using flower_type ID
        $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE flower_id = :flower_id");
        $stmt->execute(['flower_id' => $item['flower_type']]);
        $flower = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($flower) {
            $flower_name = $flower['name'];
            $flower_price = $flower['price'];
        } else {
            $flower_name = "Unknown Flower"; // Default value
            $flower_price = 0; // Default price
        }

        // Fetch container details using container_type ID
        $stmt = $pdo->prepare("SELECT container_name, price FROM container WHERE container_id = :container_id");
        $stmt->execute(['container_id' => $item['container_type']]);
        $container = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($container) {
            $container_name = $container['container_name'];
            $container_price = $container['price'];
        } else {
            $container_name = "Unknown Container"; // Default value
            $container_price = 0; // Default price
        }

        // Fetch color details using container_color ID
        $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = :color_id");
        $stmt->execute(['color_id' => $item['container_color']]);
        $color = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($color) {
            $color_name = $color['color_name'];
            $color_price = 0; // No extra cost for color
        } else {
            $color_name = "Unknown Color"; // Default value
            $color_price = 0; // No extra cost
        }

        // Calculate total price for this flower set
        $item_total_price = ($flower_price * $item['num_flowers']) + $container_price + $color_price;
        $total_price += $item_total_price;
    ?>
        <div class="customization-item">
            <h4>Flower Set <?php echo $index + 1; ?>:</h4>
            <p><strong>Flower Type:</strong> <?php echo htmlspecialchars($flower_name); ?> ($<?php echo number_format($flower_price, 2); ?> per flower)</p>
            <p><strong>Number of Flowers:</strong> <?php echo htmlspecialchars($item['num_flowers']); ?></p>
            <p><strong>Container Type:</strong> <?php echo htmlspecialchars($container_name); ?> ($<?php echo number_format($container_price, 2); ?>)</p>
            <p><strong>Container Color:</strong> <?php echo htmlspecialchars($color_name); ?> (No extra cost)</p>
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
