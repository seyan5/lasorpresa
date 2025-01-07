<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected flower type, size, and color
    $selected_types = isset($_POST['type']) ? $_POST['type'] : [];
    $selected_sizes = isset($_POST['size']) ? $_POST['size'] : [];
    $selected_colors = isset($_POST['color']) ? $_POST['color'] : [];

    // Example: Show the selected options (You can process further)
    echo "<h3>Your Customized Bouquet:</h3>";
    
    // Flower Types
    echo "<p>Flower Type(s): " . implode(", ", $selected_types) . "</p>";
    
    // Sizes
    echo "<p>Size(s): " . implode(", ", $selected_sizes) . "</p>";
    
    // Colors
    echo "<p>Color(s): " . implode(", ", $selected_colors) . "</p>";
    
    // Optionally, you can store this information in the database or show a summary page
}
?>
