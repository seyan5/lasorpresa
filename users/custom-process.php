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
    
    // Flower Types, Quantities, and Prices
    echo "<p>Flower Type(s), Quantity, and Price:</p>";
    foreach ($selected_types as $index => $flower_id) {
        // Get the flower name and price from the database using the function
        list($flower_name, $flower_price) = getFlowerDetailsById($flower_id); 
        $quantity = isset($selected_quantities[$index]) ? $selected_quantities[$index] : 1; // Default to 1 if no quantity is selected
        $total_price = $flower_price * $quantity; // Calculate the total price for the current flower

        echo "<p>{$flower_name}: {$quantity} flower(s) at \${$flower_price} each, Total: \${$total_price}</p>";
    }

    // Sizes
    echo "<p>Size(s): " . implode(", ", $selected_sizes) . "</p>";
    
    // Colors
    echo "<p>Color(s): " . implode(", ", $selected_colors) . "</p>";
    
    // Optionally, you can store this information in the database or show a summary page
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
