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
        // Add container price only once per group
        $stmt = $pdo->prepare("SELECT price FROM container WHERE container_id = :container_id");
        $stmt->execute(['container_id' => $item['container_type']]);
        $container = $stmt->fetch(PDO::FETCH_ASSOC);
        $container_price = $container['price'] ?? 0;

        $grouped_customization[$key] = [
            'container_type' => $item['container_type'],
            'container_color' => $item['container_color'],
            'remarks' => $item['remarks'] ?? 'No remarks provided',
            'flowers' => [],
            'container_price' => $container_price, // Store container price for the group
            'expected_image' => $item['expected_image'] ?? "../images/previews/default.jpg", // Use uploaded image or default
        ];

        $total_price += $container_price; // Add container price to total price only once
    }

    $grouped_customization[$key]['flowers'][] = $item;

    // Calculate the total price for the flower
    $stmt = $pdo->prepare("SELECT price FROM flowers WHERE id = :flower_id");
    $stmt->execute(['flower_id' => $item['flower_type']]);
    $flower = $stmt->fetch(PDO::FETCH_ASSOC);
    $flower_price = $flower['price'] ?? 0;

    $total_price += $flower_price * $item['num_flowers']; // Add flower price to total
}

// Save total price in session for use in checkout
$_SESSION['total_price'] = $total_price;
?>

<h3>Your Floral Arrangement Customization</h3>

<div class="customization-summary">
    <?php foreach ($grouped_customization as $key => $group): ?>
        <?php
        // Fetch container details
        $stmt = $pdo->prepare("SELECT container_name FROM container WHERE container_id = :container_id");
        $stmt->execute(['container_id' => $group['container_type']]);
        $container = $stmt->fetch(PDO::FETCH_ASSOC);
        $container_name = $container['container_name'] ?? "Unknown Container";

        // Fetch color details
        $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = :color_id");
        $stmt->execute(['color_id' => $group['container_color']]);
        $color = $stmt->fetch(PDO::FETCH_ASSOC);
        $color_name = $color['color_name'] ?? "Unknown Color";

        // Use the expected_image from the grouped data
        $expected_image = $group['expected_image'];
        ?>

        <div class="customization-item">
            <div style="display: flex; align-items: center; gap: 20px;">
                <img src="<?php echo htmlspecialchars("uploads/" . $expected_image); ?>" alt="Customization Preview"
                    style="width: 300px; height: auto;">
                <div>
                    <p><strong>Container Type:</strong> <?php echo htmlspecialchars($container_name); ?>
                        ($<?php echo number_format($group['container_price'], 2); ?>)</p>
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
