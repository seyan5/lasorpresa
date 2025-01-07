<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected flower type, size, color, and quantity
    $selected_types = isset($_POST['type']) ? $_POST['type'] : [];
    $selected_sizes = isset($_POST['size']) ? $_POST['size'] : [];
    $selected_colors = isset($_POST['color']) ? $_POST['color'] : [];
    $selected_quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];

    // Example: Show the selected options (You can process further)
    echo "<h3>Your Customized Bouquet:</h3>";
    
    // Flower Types and Quantities
    echo "<p>Flower Type(s) and Quantity:</p>";
    foreach ($selected_types as $index => $flower_id) {
        // Get the flower name from the ID (or any other relevant details from the database)
        $flower_name = getFlowerNameById($flower_id); // Assuming a function to fetch flower name
        $quantity = isset($selected_quantities[$index]) ? $selected_quantities[$index] : 1; // Default to 1 if no quantity is selected
        echo "<p>{$flower_name}: {$quantity} flower(s)</p>";
    }

    // Sizes
    echo "<p>Size(s): " . implode(", ", $selected_sizes) . "</p>";
    
    // Colors
    echo "<p>Color(s): " . implode(", ", $selected_colors) . "</p>";
    
    // Optionally, you can store this information in the database or show a summary page
}

// Function to get the flower name by ID (This is just an example, replace it with actual database logic)
function getFlowerNameById($id) {
    // Example: Return the flower name based on the ID
    $flowers = [
        1 => 'Rose',
        2 => 'Tulip',
        3 => 'Lily',
        // Add more flower names here
    ];
    
    return isset($flowers[$id]) ? $flowers[$id] : 'Unknown Flower';
}
?>
