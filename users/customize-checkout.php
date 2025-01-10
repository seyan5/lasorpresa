<?php 
echo "<pre>";
print_r($_SESSION['customization']);
echo "</pre>";


session_start();

if (!isset($_SESSION['customization'])) {
    echo "No customization found. Please go back and customize your arrangement.";
    exit;
}

$customization = $_SESSION['customization'];


?>




<h3>Your Floral Arrangement Customization</h3>

<div class="customization-summary">
    <?php 
    $total_price = 0; // Initialize total price for display
    foreach ($customization as $index => $item): 
        // Fetch flower details using flower_type ID
        $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = :flower_id");
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

        // Determine the preview image based on customization
        $preview_image = "images/previews/default.jpg"; // Default preview
        if ($item['flower_type'] == 1 && $item['num_flowers'] == 1 && strtolower($item['container_type']) == 'wrapper' && strtolower($item['container_color']) == 'blue') {
            $preview_image = "images/previews/tulip_blue_wrapper.jpg";
        } elseif ($item['flower_type'] == 2 && $item['num_flowers'] > 1 && strtolower($item['container_type']) == 'basket') {
            $preview_image = "images/previews/rose_red_basket.jpg";
        }
    ?>
        <div class="customization-item">
            <h4>Flower Set <?php echo $index + 1; ?>:</h4>
            <!-- Preview Image -->
            <img src="<?php echo htmlspecialchars($preview_image); ?>" alt="Customization Preview" class="preview-img" style="width: 200px; height: auto;">

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
