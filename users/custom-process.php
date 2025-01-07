<?php
require 'header.php';


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
        // Get the flower name from the database using the function
        $flower_name = getFlowerNameById($flower_id); 
        $quantity = isset($selected_quantities[$index]) ? $selected_quantities[$index] : 1; // Default to 1 if no quantity is selected
        echo "<p>{$flower_name}: {$quantity} flower(s)</p>";
    }

    // Sizes
    echo "<p>Size(s): " . implode(", ", $selected_sizes) . "</p>";
    
    // Colors
    echo "<p>Color(s): " . implode(", ", $selected_colors) . "</p>";
    
    // Optionally, you can store this information in the database or show a summary page
}

function getFlowerNameById($id) {
    global $pdo; // Assuming you have a PDO connection already established

    // Prepare a query to fetch the flower name by its ID
    $statement = $pdo->prepare("SELECT name FROM flowers WHERE id = :id LIMIT 1");
    $statement->bindParam(':id', $id, PDO::PARAM_INT); // Bind the flower ID to the query
    $statement->execute();

    // Fetch the flower name
    $flower = $statement->fetch(PDO::FETCH_ASSOC);
    
    // Return the flower name or a default value if not found
    return $flower ? $flower['name'] : 'Unknown Flower';
}
?>
