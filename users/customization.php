<?php
require_once('header.php');  // Include header if necessary (navigation, etc.)
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Customize Your Floral Arrangement</h2>
                <form id="floral-customization-form" action="customization-submit.php" method="POST">
                    <div id="flower-container">
                        <!-- Flower 1 -->
                        <div class="flower-item" id="flower-item-1">
                            <div class="form-group">
                                <label for="flower_type_1">Choose Flower Type:</label>
                                <select id="flower_type_1" name="flower_type[]" class="form-control" required>
                                    <option value="roses">Roses</option>
                                    <option value="tulips">Tulips</option>
                                    <option value="lilies">Lilies</option>
                                    <option value="daisies">Daisies</option>
                                    <option value="sunflowers">Sunflowers</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="flower_color_1">Choose Flower Color:</label>
                                <select id="flower_color_1" name="flower_color[]" class="form-control" required>
                                    <option value="red">Red</option>
                                    <option value="yellow">Yellow</option>
                                    <option value="white">White</option>
                                    <option value="pink">Pink</option>
                                    <option value="purple">Purple</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="num_flowers_1">Number of Flowers:</label>
                                <input type="number" id="num_flowers_1" name="num_flowers[]" class="form-control" min="1" max="100" value="1" required>
                            </div>

                            <div class="form-group">
                                <label for="container_type_1">Choose Container:</label>
                                <select id="container_type_1" name="container_type[]" class="form-control" required>
                                    <option value="vase">Vase</option>
                                    <option value="basket">Basket</option>
                                    <option value="box">Box</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Button to add more flowers -->
                    <button type="button" class="btn btn-secondary" id="add-flower-btn">Add Another Flower</button>

                    <!-- Display Selections -->
                    <div class="form-group">
                        <h4>Your Selection</h4>
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
        const flowerColors = document.querySelectorAll('[id^="flower_color_"]');
        const numFlowers = document.querySelectorAll('[id^="num_flowers_"]');
        const containerTypes = document.querySelectorAll('[id^="container_type_"]');
        
        flowerTypes.forEach((flowerType, index) => {
            const flowerColor = flowerColors[index].value;
            const numFlower = numFlowers[index].value;
            const containerType = containerTypes[index].value;

            const selectionSummary = `
                <p><strong>Flower ${index + 1}</strong></p>
                <p>Flower Type: ${flowerType.value}</p>
                <p>Flower Color: ${flowerColor}</p>
                <p>Number of Flowers: ${numFlower}</p>
                <p>Container: ${containerType}</p>
                <hr>
            `;
            selectedSelections.innerHTML += selectionSummary;
        });
    }

    // Add another flower option
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
                <label for="flower_color_${flowerCount}">Choose Flower Color:</label>
                <select id="flower_color_${flowerCount}" name="flower_color[]" class="form-control" required>
                    <option value="red">Red</option>
                    <option value="yellow">Yellow</option>
                    <option value="white">White</option>
                    <option value="pink">Pink</option>
                    <option value="purple">Purple</option>
                </select>
            </div>

            <div class="form-group">
                <label for="num_flowers_${flowerCount}">Number of Flowers:</label>
                <input type="number" id="num_flowers_${flowerCount}" name="num_flowers[]" class="form-control" min="1" max="100" value="1" required>
            </div>

            <div class="form-group">
                <label for="container_type_${flowerCount}">Choose Container:</label>
                <select id="container_type_${flowerCount}" name="container_type[]" class="form-control" required>
                    <option value="vase">Vase</option>
                    <option value="basket">Basket</option>
                    <option value="box">Box</option>
                </select>
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

