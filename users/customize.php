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
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
            <button type="button" class="btn btn-success add-flower-btn">Add Flower</button>
            <div class="form-group flower-type">
                <label for="type" class="col-sm-3 control-label">Select Flower Type</label>
                <div class="col-sm-4">
                <select name="type[]" class="form-control select2 flower-type-select" multiple="multiple">
    <?php
    $statement = $pdo->prepare("SELECT * FROM flowers ORDER BY id ASC");
    $statement->execute();
    $types = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($types as $row) {
        echo "<option value='{$row['id']}' data-quantity='{$row['quantity']}' data-price='{$row['price']}'>{$row['name']}</option>";
    }
    ?>
</select>
                    <!-- Flower Quantity Slider -->
                    <div class="flower-quantity-container"></div> <!-- Container for quantity slider inside col-sm-4 -->
                </div>
                <div class="col-sm-4">
                </div>
            </div>
        </div>

        <!-- Dynamic Flower Quantity Selection (for added flowers) -->
        <div id="quantity-section-container"></div>


            <!-- Size of Flower Selection -->
            <div class="form-group">
                <label for="size" class="col-sm-3 control-label">Select Size</label>
                <div class="col-sm-4">
                    <select name="size[]" class="form-control select2" multiple="multiple">
                        <?php
                        $statement = $pdo->prepare("SELECT * FROM type ORDER BY type_id ASC");
                        $statement->execute();
                        $sizes = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($sizes as $row) {
                            echo "<option value='{$row['type_id']}'>{$row['type_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Color Selection -->
            <div class="form-group">
                <label for="color" class="col-sm-3 control-label">Select Color</label>
                <div class="col-sm-4">
                    <select name="color[]" class="form-control select2" multiple="multiple">
                        <?php
                        $statement = $pdo->prepare("SELECT * FROM color ORDER BY color_id ASC");
                        $statement->execute();
                        $colors = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($colors as $row) {
                            echo "<option value='{$row['color_id']}'>{$row['color_name']}</option>";
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
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
   $(document).ready(function() {
    // Initialize the select2 plugin
    $('.select2').select2();

    // Add new flower type dropdown dynamically
    $(document).on('click', '.add-flower-btn', function() {
        var flowerTypeHTML = `
            <div class="form-group flower-type">
                <label for="type" class="col-sm-3 control-label">Select Flower Type</label>
                <div class="col-sm-4">
                    <select name="type[]" class="form-control select2 flower-type-select" multiple="multiple">
                        <?php
                        $statement = $pdo->prepare("SELECT * FROM flowers ORDER BY id ASC");
                        $statement->execute();
                        $types = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($types as $row) {
                            echo "<option value='{$row['id']}' data-quantity='{$row['quantity']}' data-price='{$row['price']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                    <div class="flower-quantity-container"></div>
                </div>
                <div class="col-sm-4">
                    <button type="button" class="btn btn-danger remove-flower-btn">x</button>
                </div>
            </div>
        `;
        $('#flower-types-container').append(flowerTypeHTML);
        $('.select2').select2(); // Reinitialize select2 for newly added elements
    });

    // Remove flower type section
    $(document).on('click', '.remove-flower-btn', function() {
        $(this).closest('.flower-type').remove();
        calculateTotalPrice(); // Recalculate the total price
    });

    // Display quantity slider input field inside col-sm-4
    $(document).on('change', '.flower-type-select', function() {
        var flowerContainer = $(this).closest('.flower-type');
        flowerContainer.find('.flower-quantity-container').empty(); // Clear any existing quantity sliders

        var selectedFlowerIds = $(this).val(); // Get selected flower IDs
        selectedFlowerIds.forEach(function(flowerId) {
            var selectedFlower = $("option[value='" + flowerId + "']");
            var flowerName = selectedFlower.text();
            var maxQuantity = selectedFlower.data('quantity');
            var flowerPrice = selectedFlower.data('price'); // Get the price from the data-price attribute

            // Create the HTML for the quantity slider
            var quantitySliderHTML = `
                <div class="form-group flower-quantity">
                    <label for="quantity">Quantity</label>
                    <input type="range" name="quantity[]" class="form-control quantity-slider" min="1" max="${maxQuantity}" value="1" data-price="${flowerPrice}" id="slider-${flowerId}">
                    <output for="slider-${flowerId}" class="quantity-output">1</output>
                </div>
                <div class="total-price-container">
                    <span class="flower-price">Price: $<span class="price">${flowerPrice}</span></span>
                </div>
            `;

            // Append the slider and price display to the quantity container
            flowerContainer.find('.flower-quantity-container').append(quantitySliderHTML);
        });

        // Update the slider value display and price when the user interacts with the slider
        $(document).on('input', '.quantity-slider', function() {
            var quantity = $(this).val();
            var price = $(this).data('price');
            var totalPrice = quantity * price;

            // Update the quantity display
            $(this).next('.quantity-output').text(quantity);

            // Update the price display
            $(this).closest('.flower-quantity').find('.price').text(totalPrice.toFixed(2));

            // Recalculate the total price on input
            calculateTotalPrice();
        });
    });

    // Trigger the quantity slider for the first flower type on page load if a flower type is selected
    $('#flower-type').each(function() {
        var flowerContainer = $(this).closest('.flower-type');
        var selectedFlowerIds = $(this).val();
        selectedFlowerIds.forEach(function(flowerId) {
            var selectedFlower = $("option[value='" + flowerId + "']");
            var flowerPrice = selectedFlower.data('price');

            var quantitySliderHTML = `
                <div class="form-group flower-quantity">
                    <label for="quantity">Quantity</label>
                    <input type="range" name="quantity[]" class="form-control quantity-slider" min="1" max="${selectedFlower.data('quantity')}" value="1" data-price="${flowerPrice}" id="slider-${flowerId}">
                    <output for="slider-${flowerId}" class="quantity-output">1</output>
                </div>
                <div class="total-price-container">
                    <span class="flower-price">Price: $<span class="price">${flowerPrice}</span></span>
                </div>
            `;

            flowerContainer.find('.flower-quantity-container').append(quantitySliderHTML);
        });

        $(this).trigger('change');
    });

    // Function to calculate the total price for all flowers
    function calculateTotalPrice() {
        var total = 0;
        $('.quantity-slider').each(function() {
            var quantity = $(this).val();
            var price = $(this).data('price');
            total += quantity * price;
        });
        // Display the total price
        $('#total-price').text('Total Price: $' + total.toFixed(2));
    }

    // Trigger change event for the first flower type on page load if any options are selected
    if ($('#flower-type').val() !== null) {
        $('#flower-type').trigger('change');
    }
});


</script>

