<?php
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected flower type, size, color, and quantity
    $selected_types = isset($_POST['type']) ? $_POST['type'] : [];
    $selected_sizes = isset($_POST['size']) ? $_POST['size'] : [];
    $selected_colors = isset($_POST['color']) ? $_POST['color'] : [];
    $selected_quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];

    // Initialize the overall total price
    $overall_total_price = 0;

    echo "<h3>Your Customized Bouquet:</h3>";
    
    // Start the table
    echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
    echo "<thead>
            <tr>
                <th>Flower Type</th>
                <th>Quantity</th>
                <th>Price per Flower</th>
                <th>Total Price</th>
            </tr>
          </thead>";
    echo "<tbody>";
    
    // Flower Types, Quantities, and Prices
    foreach ($selected_types as $index => $flower_id) {
        // Get the flower name and price from the database using the function
        list($flower_name, $flower_price) = getFlowerDetailsById($flower_id);
        $quantity = isset($selected_quantities[$index]) ? $selected_quantities[$index] : 1; // Default to 1 if no quantity is selected
        $total_price = $flower_price * $quantity; // Calculate the total price for the current flower
        
        // Add the flower's total price to the overall total price
        $overall_total_price += $total_price;

        // Display the row for this flower
        echo "<tr>
                <td>{$flower_name}</td>
                <td>{$quantity}</td>
                <td>\${$flower_price}</td>
                <td>\${$total_price}</td>
              </tr>";
    }
    
    // End the table
    echo "</tbody>";
    echo "</table>";

    // Display the selected sizes and colors with their names
    echo "<p><strong>Size(s):</strong> " . implode(", ", mapSizes($selected_sizes)) . "</p>";
    echo "<p><strong>Color(s):</strong> " . implode(", ", mapColors($selected_colors)) . "</p>";

    // Display the overall total price
    echo "<p><strong>Overall Total Price: \${$overall_total_price}</strong></p>";
}

// Function to map size IDs to names
function mapSizes($size_ids) {
    $size_map = [
        1 => 'Small',
        2 => 'Medium',
        3 => 'Large'
    ];

    $sizes = [];
    foreach ($size_ids as $size_id) {
        if (isset($size_map[$size_id])) {
            $sizes[] = $size_map[$size_id];
        } else {
            $sizes[] = 'Unknown Size';
        }
    }
    
    return $sizes;
}

// Function to map color IDs to names
function mapColors($color_ids) {
    $color_map = [
        1 => 'Red',
        2 => 'White',
        3 => 'Pink',
        4 => 'Yellow',
        5 => 'Blue'
    ];

    $colors = [];
    foreach ($color_ids as $color_id) {
        if (isset($color_map[$color_id])) {
            $colors[] = $color_map[$color_id];
        } else {
            $colors[] = 'Unknown Color';
        }
    }
    
    return $colors;
}

function getFlowerDetailsById($id) {
    global $pdo; // Assuming you have a PDO connection already established

    // Prepare a query to fetch the flower name and price by its ID
    $statement = $pdo->prepare("SELECT name, price FROM flowers WHERE id = :id LIMIT 1");
    $statement->bindParam(':id', $id, PDO::PARAM_INT); // Bind the flower ID to the query
    $statement->execute();

    // Fetch the flower details
    $flower = $statement->fetch(PDO::FETCH_ASSOC);
    
    // Return the flower name and price or default values if not found
    return $flower ? [$flower['name'], $flower['price']] : ['Unknown Flower', 0];
}
?>
