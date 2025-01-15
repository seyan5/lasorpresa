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

        // Determine the preview image based on the first flower in the group
        $first_flower = $group['flowers'][0];
        $preview_images = [
            2 => [ // Flower type 2 (e.g., Rose)
                1 => [
                    1 => "../images/previews/rose_red_basket.jpg",
                    2 => "../images/previews/rose_red_wrapper.jpg",
                    4 => "../images/previews/rose_red_vase.jpg",
                ],
                2 => [
                    1 => "../images/previews/rose_red_basket2.jpg",
                    2 => "../images/previews/rose_red_wrapper2.jpg",
                    4 => "../images/previews/rose_red_vase2.jpg",
                ],
                3 => [
                    1 => "../images/previews/rose_red_basket3.jpg",
                    2 => "../images/previews/rose_red_wrapper3.jpg",
                    4 => "../images/previews/rose_red_vase3.jpg",
                ]
            ],
            3 => [ // Flower type 3 (e.g., Tulip)
                1 => [
                    1 => "../images/previews/tulip_red_basket.jpg",
                    2 => "../images/previews/tulip_red_wrapper.jpg",
                    4 => "../images/previews/tulip_red_vase.jpg",
                ],
                2 => [
                    1 => "../images/previews/tulip_red_basket2.jpg",
                    2 => "../images/previews/tulip_red_wrapper2.jpg",
                    4 => "../images/previews/tulip_red_vase2.jpg",
                ],
                3 => [
                    1 => "../images/previews/tulip_red_basket3.jpg",
                    2 => "../images/previews/tulip_red_wrapper3.jpg",
                    4 => "../images/previews/tulip_red_vase3.jpg",
                ]
             ],
            
        ];
        $preview_image = "../images/previews/default.jpg";
        if (isset($preview_images[$first_flower['flower_type']][$first_flower['num_flowers']][$group['container_type']])) {
            $preview_image = $preview_images[$first_flower['flower_type']][$first_flower['num_flowers']][$group['container_type']];
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
