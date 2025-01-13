<?php
require_once('conn.php');

// Database connection using PDO (already in your code)

// Fetch container types from the 'container' table
$container_types = [];
$container_query = $pdo->prepare("SELECT * FROM `container`");
$container_query->execute();
$container_types = $container_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch container colors from the 'color' table
$container_colors = [];
$color_query = $pdo->prepare("SELECT * FROM `color`");
$color_query->execute();
$container_colors = $color_query->fetchAll(PDO::FETCH_ASSOC);

// Fetch flower types from the 'flowers' table
$flower_types = [];
$flower_query = $pdo->prepare("SELECT * FROM `flowers`");
$flower_query->execute();
$flower_types = $flower_query->fetchAll(PDO::FETCH_ASSOC);

// Check if the form was submitted and handle form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Make sure data is set before accessing
    $container_type = isset($_POST['container_type']) ? $_POST['container_type'] : 'Not selected';
    $container_color = isset($_POST['container_color']) ? $_POST['container_color'] : 'Not selected';

    // Flower data handling: This may be an array, so loop through them
    $flower_types_selected = isset($_POST['flower_type']) ? $_POST['flower_type'] : [];
    $num_flowers = isset($_POST['num_flowers']) ? $_POST['num_flowers'] : [];
} else {
    // Default values when form is not yet submitted
    $container_type = 'Not selected';
    $container_color = 'Not selected';
    $flower_types_selected = [];
    $num_flowers = [];
}

if (!isset($_SESSION['customization'])) {
    echo "No customization found. Please go back and customize your arrangement.";
    exit;
}

$customization = $_SESSION['customization'];
?>

<!-- Include Bootstrap CSS -->
<link rel="stylesheet" href="../css/customize.css?v=1.2">

<header>
        <a href="index.php" class="back">← Back to Home Page</a>
        <a href="customize-checkout.php" class="back">Check Out Cart</a>

    </header>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Customize Your Floral Arrangement</h2>
                <form id="floral-customization-form" action="customization-submit.php" method="POST">
                    <!-- Container Customization Section -->
                    <div class="section">
                        <h4>Container Customization</h4>
                        <div class="form-group">
                            <label for="container_type">Choose Container Type:</label>
                            <select id="container_type" name="container_type" class="form-control" required>
                                <?php foreach ($container_types as $container): ?>
                                    <option value="<?= $container['container_id'] ?>" <?= ($container_type == $container['container_id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($container['container_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="container_color">Choose Container Color:</label>
                            <select id="container_color" name="container_color" class="form-control" required>
                                <?php foreach ($container_colors as $color): ?>
                                    <option value="<?= $color['color_id'] ?>" <?= ($container_color == $color['color_id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($color['color_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Flower Customization Section -->
                    <div id="flower-container">
                        <h4>Flower Customization</h4>
                        <?php foreach ($flower_types_selected as $index => $flower_type): ?>
                            <div class="flower-item" id="flower-item-<?php echo $index + 1; ?>">
                                <div class="form-group">
                                    <label for="flower_type_<?php echo $index + 1; ?>">Choose Flower Type:</label>
                                    <select id="flower_type_<?php echo $index + 1; ?>" name="flower_type[]" class="form-control" required>
                                        <?php foreach ($flower_types as $flower): ?>
                                            <option value="<?= $flower['id'] ?>" <?= ($flower_type == $flower['id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($flower['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="num_flowers_<?php echo $index + 1; ?>">Number of Flowers:</label>
                                    <input type="number" id="num_flowers_<?php echo $index + 1; ?>" name="num_flowers[]" class="form-control" min="1" max="100" value="<?php echo isset($num_flowers[$index]) ? $num_flowers[$index] : 1; ?>" required>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Button to add more flowers -->
                    <button type="button" class="btn btn-secondary" id="add-flower-btn">Add Another Flower</button>

                    <!-- Real-time Selections -->
                    <div class="section">
                        <h4>Your Selections</h4>
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

        // Preview images array
        $preview_images = [
            2 => [ // Flower type 2 (e.g., Rose)
                1 => [
                    1 => "../images/previews/rose_red_basket.jpg", // 1 flower, basket, color 1
                    2 => "../images/previews/rose_red_wrapper.jpg", // 1 flower, wrapper, color 1
                    4 => "../images/previews/rose_red_vase.jpg", // 1 flower, vase, color 1
                ],
                2 => [
                    1 => "../images/previews/rose_red_basket2.jpg", // 2 flowers, basket, color 1
                    2 => "../images/previews/rose_red_wrapper2.jpg", // 2 flowers, wrapper, color 1
                    4 => "../images/previews/rose_red_vase2.jpg", // 2 flowers, vase, color 1
                ],
                3 => [
                    1 => "../images/previews/rose_red_basket3.jpg", // 3 flowers, basket, color 1
                    2 => "../images/previews/rose_red_wrapper3.jpg", // 3 flowers, wrapper, color 1
                    4 => "../images/previews/rose_red_vase3.jpg", // 3 flowers, vase, color 1
                ]
            ],
            3 => [ // Flower type 3 (e.g., Tulip)
                1 => [
                    1 => "../images/previews/tulip_red_basket.jpg", // 1 flower, basket, color 1
                    2 => "../images/previews/tulip_red_wrapper.jpg", // 1 flower, wrapper, color 1
                    4 => "../images/previews/tulip_red_vase.jpg", // 1 flower, vase, color 1
                ],
                2 => [
                    1 => "../images/previews/tulip_red_basket2.jpg", // 2 flowers, basket, color 1
                    2 => "../images/previews/tulip_red_wrapper2.jpg", // 2 flowers, wrapper, color 1
                    4 => "../images/previews/tulip_red_vase2.jpg", // 2 flowers, vase, color 1
                ],
                3 => [
                    1 => "../images/previews/tulip_red_basket3.jpg", // 3 flowers, basket, color 1
                    2 => "../images/previews/tulip_red_wrapper3.jpg", // 3 flowers, wrapper, color 1
                    4 => "../images/previews/tulip_red_vase3.jpg", // 3 flowers, vase, color 1
                ]
            ]
        ];

        // Determine the preview image based on customization
        $preview_image = "../images/previews/default.jpg"; // Default preview
        // Check if flower type, number of flowers, and container type are set
        if (isset($preview_images[$item['flower_type']][$item['num_flowers']][$item['container_type']])) {
            // Set the preview image based on the condition
            $preview_image = $preview_images[$item['flower_type']][$item['num_flowers']][$item['container_type']];
        }
    ?>
        <div class="customization-item">
            <h4>Flower Set <?php echo $index + 1; ?>:</h4>
            <!-- Preview Image -->
            <img src="<?php echo htmlspecialchars($preview_image); ?>" alt="Customization Preview" class="preview-img" style="width: 500px; height: auto;">

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
                        <div id="selected-selections">
                            <!-- This will show the real-time selections of flowers -->
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Your Customization</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let flowerCount = 1; // Track the number of flower sections added
    const addFlowerBtn = document.getElementById('add-flower-btn');
    const flowerContainer = document.getElementById('flower-container');
    const selectedSelections = document.getElementById('selected-selections');

    // Update selection summary in real-time
    function updateSelection() {
        selectedSelections.innerHTML = ''; // Clear previous selections
        const flowerTypes = document.querySelectorAll('[id^="flower_type_"]');
        const numFlowers = document.querySelectorAll('[id^="num_flowers_"]');
        const containerType = document.getElementById('container_type').value;
        const containerColor = document.getElementById('container_color').value;

        selectedSelections.innerHTML = `
            <p><strong>Container Type:</strong> ${containerType}</p>
            <p><strong>Container Color:</strong> ${containerColor}</p>
            <hr>
        `;
        
        flowerTypes.forEach((flowerType, index) => {
            const numFlower = numFlowers[index].value;

            const selectionSummary = `
                <p><strong>Flower ${index + 1}</strong></p>
                <p>Flower Type: ${flowerType.options[flowerType.selectedIndex].text}</p>
                <p>Number of Flowers: ${numFlower}</p>
                <hr>
            `;
            selectedSelections.innerHTML += selectionSummary;
        });
    }

    // Add another flower option to the same container
    addFlowerBtn.addEventListener('click', () => {
        flowerCount++;

        const flowerItem = document.createElement('div');
        flowerItem.classList.add('flower-item');
        flowerItem.id = `flower-item-${flowerCount}`;
        
        // Generate new flower customization form fields
        flowerItem.innerHTML = `
            <div class="form-group">
                <label for="flower_type_${flowerCount}">Choose Flower Type:</label>
                <select id="flower_type_${flowerCount}" name="flower_type[]" class="form-control" required>
                    <?php foreach ($flower_types as $flower): ?>
                        <option value="<?= $flower['id'] ?>"><?= htmlspecialchars($flower['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="num_flowers_${flowerCount}">Number of Flowers:</label>
                <input type="number" id="num_flowers_${flowerCount}" name="num_flowers[]" class="form-control" min="1" max="100" value="1" required>
            </div>
        `;

        // Append the new flower item
        flowerContainer.appendChild(flowerItem);

        // Update selection summary
        updateSelection();
    });

    // Listen to the form inputs to update the selection in real-time
    flowerContainer.addEventListener('input', updateSelection);
    flowerContainer.addEventListener('change', updateSelection);

    // Initial update on page load
    updateSelection();
</script>
