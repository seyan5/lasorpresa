<?php
require 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Flower Customization</title>
  <link rel="stylesheet" href="../css/customize.css">
</head>
<body>
  <div class="container">
    <header class="header">
      <a href="index.php" class="back-link">
        <span class="back-arrow">←</span> La Sorpresa Home Page
      </a>
    </header>
    
    <main class="main-content">
      <section class="image-section">
        <h2>Customization</h2>
        <img src="../ivd/flower.png" alt="Flower Bouquet" class="bouquet-image">
      </section>

      <div class="container">
        <h2>Flower Customization</h2>
        <form method="POST" action="custom-process.php" id="customization-form">
            <!-- Flower Types Section -->
            <div id="flower-types-container">
                <div class="form-group flower-type">
                    <label for="type" class="col-sm-3 control-label">Select Flower Type</label>
                    <div class="col-sm-4">
                        <select name="type[]" class="form-control select2" multiple="multiple" id="flower-type">
                            <?php
                            // Fetch flower types from the database
                            $statement = $pdo->prepare("SELECT * FROM flowers ORDER BY id ASC");
                            $statement->execute();
                            $types = $statement->fetchAll(PDO::FETCH_ASSOC);
                            if ($types) {
                                foreach ($types as $row) {
                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                }
                            } else {
                                echo "<option>No flower types available</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Add and Remove buttons -->
                    <div class="col-sm-4">
                        <button type="button" class="btn btn-success add-flower-btn">Add Flower</button>
                        <button type="button" class="btn btn-danger remove-flower-btn">Remove Flower</button>
                    </div>
                </div>
            </div>

            <!-- Button to Add New Flower Type Dropdown -->
            <button type="button" class="btn btn-secondary" id="add-flower-btn">Add Flower</button>

            <!-- Size of Flower Selection -->
            <div class="form-group">
                <label for="size" class="col-sm-3 control-label">Select Size</label>
                <div class="col-sm-4">
                    <select name="size[]" class="form-control select2" multiple="multiple" id="flower-size">
                        <?php
                        // Fetch flower sizes from the database (ensure this table exists or adjust as needed)
                        $statement = $pdo->prepare("SELECT * FROM type ORDER BY type_id ASC");
                        $statement->execute();
                        $sizes = $statement->fetchAll(PDO::FETCH_ASSOC);
                        if ($sizes) {
                            foreach ($sizes as $row) {
                                echo "<option value='{$row['type_id']}'>{$row['type_name']}</option>";
                            }
                        } else {
                            echo "<option>No sizes available</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Color Selection -->
            <div class="form-group">
                <label for="color" class="col-sm-3 control-label">Select Color</label>
                <div class="col-sm-4">
                    <select name="color[]" class="form-control select2" multiple="multiple" id="flower-color">
                        <?php
                        // Fetch flower colors from the database
                        $statement = $pdo->prepare("SELECT * FROM color ORDER BY color_id ASC");
                        $statement->execute();
                        $colors = $statement->fetchAll(PDO::FETCH_ASSOC);
                        if ($colors) {
                            foreach ($colors as $row) {
                                echo "<option value='{$row['color_id']}'>{$row['color_name']}</option>";
                            }
                        } else {
                            echo "<option>No colors available</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <div class="col-sm-4">
                    <button type="submit" class="btn btn-primary">Customize Your Bouquet</button>
                </div>
            </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>

<!-- Include jQuery and select2 Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize the select2 plugin
        $('.select2').select2();

        // Add new flower type dropdown with Add and Remove buttons on click
        $('#add-flower-btn').on('click', function() {
            var flowerTypeHTML = `
                <div class="form-group flower-type">
                    <label for="type" class="col-sm-3 control-label">Select Flower Type</label>
                    <div class="col-sm-4">
                        <select name="type[]" class="form-control select2" multiple="multiple">
                            <?php
                            // Fetch flower types from the database
                            $statement = $pdo->prepare("SELECT * FROM flowers ORDER BY id ASC");
                            $statement->execute();
                            $types = $statement->fetchAll(PDO::FETCH_ASSOC);
                            if ($types) {
                                foreach ($types as $row) {
                                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                }
                            } else {
                                echo "<option>No flower types available</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <!-- Add and Remove buttons for each flower -->
                    <div class="col-sm-4">
                        <button type="button" class="btn btn-success add-flower-btn">Add Flower</button>
                        <button type="button" class="btn btn-danger remove-flower-btn">Remove Flower</button>
                    </div>
                </div>
            `;

            // Append the new flower type dropdown to the container
            $('#flower-types-container').append(flowerTypeHTML);

            // Reinitialize select2 for the newly added select elements
            $('.select2').select2();
        });

        // Handle the add flower button for each flower section
        $(document).on('click', '.add-flower-btn', function() {
            var flowerTypeSelect = $(this).closest('.flower-type').find('select');
            var selectedFlower = flowerTypeSelect.val();
            // Add the selected flower type to an array or perform other actions
            console.log('Flower Added:', selectedFlower);
        });

        // Handle the remove flower button for each flower section
        $(document).on('click', '.remove-flower-btn', function() {
            $(this).closest('.flower-type').remove();
        });
    });
</script>
