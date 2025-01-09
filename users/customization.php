<?php
require_once('header.php');  // Include header if necessary (navigation, etc.)
?>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Customize Your Floral Arrangement</h2>
                <form id="floral-customization-form" action="customization-submit.php" method="POST">
                    <!-- Flower Type Selection -->
                    <div class="form-group">
                        <label for="flower_type">Choose Flower Type:</label>
                        <select id="flower_type" name="flower_type" class="form-control" required>
                            <option value="roses">Roses</option>
                            <option value="tulips">Tulips</option>
                            <option value="lilies">Lilies</option>
                            <option value="daisies">Daisies</option>
                            <option value="sunflowers">Sunflowers</option>
                        </select>
                    </div>

                    <!-- Flower Color Selection -->
                    <div class="form-group">
                        <label for="flower_color">Choose Flower Color:</label>
                        <select id="flower_color" name="flower_color" class="form-control" required>
                            <option value="red">Red</option>
                            <option value="yellow">Yellow</option>
                            <option value="white">White</option>
                            <option value="pink">Pink</option>
                            <option value="purple">Purple</option>
                        </select>
                    </div>

                    <!-- Number of Flowers -->
                    <div class="form-group">
                        <label for="num_flowers">Number of Flowers:</label>
                        <input type="number" id="num_flowers" name="num_flowers" class="form-control" min="1" max="100" value="1" required>
                    </div>

                    <!-- Container Selection -->
                    <div class="form-group">
                        <label for="container_type">Choose Container:</label>
                        <select id="container_type" name="container_type" class="form-control" required>
                            <option value="vase">Vase</option>
                            <option value="basket">Basket</option>
                            <option value="box">Box</option>
                        </select>
                    </div>

                    <!-- Display Selections -->
                    <div class="form-group">
                        <h4>Your Selection</h4>
                        <p id="selected_flower_type">Flower Type: None</p>
                        <p id="selected_flower_color">Flower Color: None</p>
                        <p id="selected_num_flowers">Number of Flowers: 1</p>
                        <p id="selected_container">Container: None</p>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Your Customization</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // JavaScript to update the selection summary in real-time
    document.getElementById('flower_type').addEventListener('change', updateSelection);
    document.getElementById('flower_color').addEventListener('change', updateSelection);
    document.getElementById('num_flowers').addEventListener('input', updateSelection);
    document.getElementById('container_type').addEventListener('change', updateSelection);

    function updateSelection() {
        const flowerType = document.getElementById('flower_type').value;
        const flowerColor = document.getElementById('flower_color').value;
        const numFlowers = document.getElementById('num_flowers').value;
        const containerType = document.getElementById('container_type').value;

        document.getElementById('selected_flower_type').textContent = 'Flower Type: ' + flowerType.charAt(0).toUpperCase() + flowerType.slice(1);
        document.getElementById('selected_flower_color').textContent = 'Flower Color: ' + flowerColor.charAt(0).toUpperCase() + flowerColor.slice(1);
        document.getElementById('selected_num_flowers').textContent = 'Number of Flowers: ' + numFlowers;
        document.getElementById('selected_container').textContent = 'Container: ' + containerType.charAt(0).toUpperCase() + containerType.slice(1);
    }

    // Initial update on page load
    updateSelection();
</script>
