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
    $remarks = isset($_POST['remarks']) ? $_POST['remarks'] : '';
} else {
    // Default values when form is not yet submitted
    $container_type = 'Not selected';
    $container_color = 'Not selected';
    $flower_types_selected = [];
    $num_flowers = [];
}
?>

<!-- Include Bootstrap CSS -->
<link rel="stylesheet" href="../css/customize.css?">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<header class="custom-header">
    <a href="index.php" class="header-link">&lt; Back to Home Page</a>
    <a href="customize-checkout.php" class="header-link">Check Out Cart&gt;</a>
</header>

<div class="custom-page">
<h2>Customize Your Floral Arrangement</h2>
    <div class="custom-container">
        <div class="custom-row">
            <!-- Left side -->
            <div class="custom-left">
                <form id="floral-customization-form" action="customization-submit.php" method="POST">
                    <!-- Container Customization Section -->
                    <div class="section">
                        <h4>Container Customization</h4>
                        <div class="form-group">
                            <label for="container_type">Choose Container Type:</label>
                            <select id="container_type" name="container_type" class="input-field" required>
                                <?php foreach ($container_types as $container): ?>
                                    <option value="<?= $container['container_id'] ?>"
                                            data-image="../admin/uploads/<?= htmlspecialchars($container['container_image']) ?>"
                                            data-price="<?= $container['price'] ?>">
                                        <?= htmlspecialchars($container['container_name']) ?> (<?= '$' . number_format($container['price'], 2) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="container_color">Choose Container Color:</label>
                            <select id="container_color" name="container_color" class="input-field" required>
                                <?php foreach ($container_colors as $color): ?>
                                    <option value="<?= $color['color_id'] ?>" <?= ($container_color == $color['color_id']) ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($color['color_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Flower Customization Section -->
                    <div class="flower-customization-container">
                        <div id="flower-container" class="flower-customization-left">
                            <h4>Flower Customization</h4>
                            <?php foreach ($flower_types_selected as $index => $flower_type): ?>
                                <div class="flower-item" id="flower-item-<?php echo $index + 1; ?>">
                                    <div class="form-group">
                                        <label for="flower_type_<?php echo $index + 1; ?>">Choose Flower Type:</label>
                                        <select id="flower_type_<?= $index + 1 ?>" name="flower_type[]" class="input-field" required>
                                            <?php foreach ($flower_types as $flower): ?>
                                                <option value="<?= $flower['id'] ?>"
                                                        data-image="../admin/uploads/<?= htmlspecialchars($flower['image']) ?>"
                                                        <?= ($flower_type == $flower['id']) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($flower['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="num_flowers_<?php echo $index + 1; ?>">Number of Flowers:</label>
                                        <input type="number" id="num_flowers_<?php echo $index + 1; ?>" name="num_flowers[]"
                                               class="input-field" min="1" max="3"
                                               value="<?php echo isset($num_flowers[$index]) ? $num_flowers[$index] : 1; ?>"
                                               required>
                                    </div>

                                    <button type="button" class="btn-remove" data-index="<?= $index + 1 ?>">X</button>
                                </div>
                            <?php endforeach; ?>

                            <!-- Button to add more flowers -->
                            <button type="button" class="btn-secondary" id="add-flower-btn">Add Flower</button>
                        </div>
                </form>
            </div>
        </div>
    </div>

    <div class="flower-customization-right">
                            <h4>Your Selections</h4>
                            <div id="selected-selections">
                                <!-- This will show the real-time selections of flowers -->
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="remarks">Remarks:</label>
                        <textarea id="remarks" name="remarks" class="input-field"
                                  placeholder="Enter any special instructions or remarks..." required></textarea>
                    </div>

                    <button type="submit" class="btn-primary">Submit Your Customization</button>
    </div>

<?php include('../loading.php'); ?>
<script>
    let flowerCount = 1; // Track the number of flower sections added
    const addFlowerBtn = document.getElementById('add-flower-btn');
    const flowerContainer = document.getElementById('flower-container');
    const selectedSelections = document.getElementById('selected-selections');

    document.getElementById('container_type').addEventListener('change', updateSelection);
document.getElementById('container_color').addEventListener('change', updateSelection);
flowerContainer.addEventListener('change', (event) => {
    if (event.target && (event.target.id.startsWith('flower_type_') || event.target.id.startsWith('num_flowers_'))) {
        updateSelection(); // Update the preview when flower type or number of flowers changes
    }
});

    // Update selection summary in real-time, including remarks
    function updateSelection() {
    selectedSelections.innerHTML = ''; // Clear previous selections
    const flowerTypes = document.querySelectorAll('[id^="flower_type_"]');
    const numFlowers = document.querySelectorAll('[id^="num_flowers_"]');
    const containerType = document.getElementById('container_type');
    const containerColor = document.getElementById('container_color');
    const remarks = document.getElementById('remarks').value;

    // Get selected container details
    const selectedContainerOption = containerType.options[containerType.selectedIndex];
    const containerImage = selectedContainerOption.getAttribute('data-image');
    const containerName = selectedContainerOption.text;

    // Display container details
    selectedSelections.innerHTML = ` 
        <p><strong>Container:</strong> ${containerName}</p>
        <img src="${containerImage}" alt="${containerName}" style="width: 150px; height: auto; margin-bottom: 10px;">
        <p><strong>Container Color:</strong> ${containerColor.options[containerColor.selectedIndex].text}</p>
        <p><strong>Remarks:</strong> ${remarks}</p>
        <hr>
    `;

    // Display selected flowers with updated number
    flowerTypes.forEach((flowerType, index) => {
        const selectedFlowerOption = flowerType.options[flowerType.selectedIndex];
        const flowerImage = selectedFlowerOption.getAttribute('data-image');
        const flowerName = selectedFlowerOption.text;
        const numFlower = numFlowers[index].value;

        const flowerSummary = `
            <p><strong>Flower ${index + 1}:</strong> ${flowerName}</p>
            <img src="${flowerImage}" alt="${flowerName}" style="width: 100px; height: auto; margin-bottom: 10px;">
            <p>Number of Flowers: ${numFlower}</p>
            <hr>
        `;
        selectedSelections.innerHTML += flowerSummary;
    });
}

    // Add event listener for the remarks field to update the live preview
    document.getElementById('remarks').addEventListener('input', updateSelection);

    // Add event listeners to flower type and number of flowers fields to update selection in real-time
    flowerContainer.addEventListener('change', (event) => {
        if (event.target && (event.target.id.startsWith('flower_type_') || event.target.id.startsWith('num_flowers_'))) {
            updateSelection(); // Update the preview on flower selection change
        }
    });

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
                        <option value="<?= $flower['id'] ?>" data-image="../admin/uploads/<?= htmlspecialchars($flower['image']) ?>"><?= htmlspecialchars($flower['name']) ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="num_flowers_${flowerCount}">Number of Flowers:</label>
                <input type="number" id="num_flowers_${flowerCount}" name="num_flowers[]" class="form-control" min="1" max="3" value="1" required>
            </div>

            <!-- Remove Flower Button -->
            <button type="button" class="btn btn-danger remove-flower-btn" data-index="${flowerCount}">X</button>
        `;
        flowerContainer.appendChild(flowerItem);

        // Update selection summary after adding a new flower
        updateSelection();
    });

    // Event delegation for dynamically created "X" remove buttons
    flowerContainer.addEventListener('click', function (event) {
        if (event.target && event.target.classList.contains('remove-flower-btn')) {
            const flowerItem = event.target.closest('.flower-item');
            flowerItem.remove();
            updateSelection(); // Update the preview after removal
        }
    });

    // Update the initial preview when the page loads
    updateSelection();
</script>
