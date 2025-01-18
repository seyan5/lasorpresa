<?php
require("conn.php");

if (!isset($_SESSION['customization'])) {
    echo "No customization found. Please go back and customize your arrangement.";
    exit;
}

$customization = $_SESSION['customization'];

// Group items by container type, color, and remarks
$grouped_customization = [];
$total_price = 0; // Initialize total price
foreach ($customization as $item) {
    $key = $item['container_type'] . '-' . $item['container_color'] . '-' . $item['remarks'];
    if (!isset($grouped_customization[$key])) {
        $grouped_customization[$key] = [
            'container_type' => $item['container_type'],
            'container_color' => $item['container_color'],
            'remarks' => $item['remarks'] ?? 'No remarks provided',
            'flowers' => []
        ];
    }
    $grouped_customization[$key]['flowers'][] = $item;

    // Calculate the total price for this item
    $stmt = $pdo->prepare("SELECT price FROM flowers WHERE id = :flower_id");
    $stmt->execute(['flower_id' => $item['flower_type']]);
    $flower = $stmt->fetch(PDO::FETCH_ASSOC);
    $flower_price = $flower['price'] ?? 0;

    $stmt = $pdo->prepare("SELECT price FROM container WHERE container_id = :container_id");
    $stmt->execute(['container_id' => $item['container_type']]);
    $container = $stmt->fetch(PDO::FETCH_ASSOC);
    $container_price = $container['price'] ?? 0;

    $total_price += ($flower_price * $item['num_flowers']) + $container_price;
}

// Save total price in session for use in checkout
$_SESSION['total_price'] = $total_price;
?>

<h3>Your Floral Arrangement Customization</h3>

<div class="customization-summary">
    <?php foreach ($grouped_customization as $key => $group): ?>
        <!-- Fetch container details -->
        <?php
        $stmt = $pdo->prepare("SELECT container_name, price FROM container WHERE container_id = :container_id");
        $stmt->execute(['container_id' => $group['container_type']]);
        $container = $stmt->fetch(PDO::FETCH_ASSOC);
        $container_name = $container['container_name'] ?? "Unknown Container";
        $container_price = $container['price'] ?? 0;

        // Fetch color details
        $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = :color_id");
        $stmt->execute(['color_id' => $group['container_color']]);
        $color = $stmt->fetch(PDO::FETCH_ASSOC);
        $color_name = $color['color_name'] ?? "Unknown Color";

       // Determine the preview image based on flower types and their quantities
$flower_counts = [2 => 0, 3 => 0, 4 => 0]; // Rose (2), Tulip (3), Lilac (4) counts
foreach ($group['flowers'] as $flower_item) {
    $flower_counts[$flower_item['flower_type']] += $flower_item['num_flowers'];
}

// Initialize preview_key as default
$preview_key = "default"; // Start with a default key

// Check which flower types are selected and update preview_key accordingly
if ($flower_counts[2] > 0) { // Rose
    $preview_key = "Rose" . $flower_counts[2]; // Example: Rose1, Rose2, etc.
}
if ($flower_counts[3] > 0) { // Tulip
    $preview_key .= "_Tulip" . $flower_counts[3]; // Example: _Tulip1, _Tulip2, etc.
}
if ($flower_counts[4] > 0) { // Lilac
    $preview_key .= "_Lilac" . $flower_counts[4]; // Example: _Lilac1, _Lilac2, etc.
}

// Debugging: echo the preview_key and path
// echo "Preview key: " . htmlspecialchars($preview_key) . "<br>";

// Check if a preview image for this combination exists
$preview_image_path = "../images/previews/" . $preview_key . ".jpg";

// Debugging: echo the generated path
// echo "Preview image path: " . htmlspecialchars($preview_image_path) . "<br>";
// echo "File exists check: " . (file_exists($preview_image_path) ? "Yes" : "No") . "<br>";

// If the preview image exists, set it, otherwise use the default
if (file_exists($preview_image_path)) {
    $preview_image = $preview_image_path;
} else {
    $preview_image = "../images/previews/default.jpg"; // Fallback to default
}

        ?>

        <div class="customization-item">
            <div style="display: flex; align-items: center; gap: 20px;">
                <img src="<?php echo htmlspecialchars($preview_image); ?>" alt="Customization Preview" style="width: 300px; height: auto;">
                <div>
                    <p><strong>Container Type:</strong> <?php echo htmlspecialchars($container_name); ?> ($<?php echo number_format($container_price, 2); ?>)</p>
                    <p><strong>Container Color:</strong> <?php echo htmlspecialchars($color_name); ?></p>
                    <p><strong>Remarks:</strong> <?php echo htmlspecialchars($group['remarks']); ?></p>
                    <h5>Flowers:</h5>
                    <ul>
                        <?php foreach ($group['flowers'] as $flower_item):
                            $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = :flower_id");
                            $stmt->execute(['flower_id' => $flower_item['flower_type']]);
                            $flower = $stmt->fetch(PDO::FETCH_ASSOC);
                            $flower_name = $flower['name'] ?? "Unknown Flower";
                            $flower_price = $flower['price'] ?? 0;

                            $flower_total_price = $flower_price * $flower_item['num_flowers'];
                            ?>
                            <li>
                                <?php echo htmlspecialchars($flower_name); ?> 
                                ($<?php echo number_format($flower_price, 2); ?> per flower) - 
                                Quantity: <?php echo htmlspecialchars($flower_item['num_flowers']); ?> 
                                (<strong>Total:</strong> $<?php echo number_format($flower_total_price, 2); ?>)
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <hr>
    <?php endforeach; ?>
</div>


<p><strong>Total Price:</strong> ₱<?php echo number_format($total_price, 2); ?></p>

<!-- Checkout Button -->
<button class="customize-checkout btn btn-primary" onclick="proceedToCheckout()">Checkout &gt;</button>

<script>
    function proceedToCheckout() {
        window.location.href = "customize-checkout.php";
    }
</script>