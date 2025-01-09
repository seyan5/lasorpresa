<?php
require_once('header.php');

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
?>

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
                <p>Flower Type: ${flowerType.value}</p>
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
                    <option value="roses">Roses</option>
                    <option value="tulips">Tulips</option>
                    <option value="lilies">Lilies</option>
                    <option value="daisies">Daisies</option>
                    <option value="sunflowers">Sunflowers</option>
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

