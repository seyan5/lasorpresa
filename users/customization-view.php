<?php

// Include database connection
require_once('header.php'); // Make sure to include your database connection

if (isset($_SESSION['customization']) && !empty($_SESSION['customization'])) {
    $customization = $_SESSION['customization'];

    echo '<h3>Your Floral Arrangement Customization</h3>';

    // Loop through each customization (for each flower)
    foreach ($customization as $index => $customize) {
        $flower_type = isset($customize['flower_type']) ? htmlspecialchars($customize['flower_type']) : 'Not selected';
        $num_flowers = isset($customize['num_flowers']) ? htmlspecialchars($customize['num_flowers']) : 'Not selected';
        $container_type_id = isset($customize['container_type']) ? $customize['container_type'] : 'Not selected';
        $container_color_id = isset($customize['container_color']) ? $customize['container_color'] : 'Not selected';

        // Fetch container type name from the database
        $container_type_name = 'Not selected';
        if ($container_type_id !== 'Not selected') {
            $stmt = $pdo->prepare("SELECT name FROM container WHERE container_id = ?");
            $stmt->execute([$container_type_id]);
            $container_type_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $container_type_name = $container_type_data['container_name'] ?? 'Not selected';
        }

        // Fetch container color name from the database
        $container_color_name = 'Not selected';
        if ($container_color_id !== 'Not selected') {
            $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = ?");
            $stmt->execute([$container_color_id]);
            $container_color_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $container_color_name = $container_color_data['color_name'] ?? 'Not selected';
        }

        echo "<p><strong>Flower Set " . ($index + 1) . ":</strong></p>";
        echo "<p><strong>Flower Type:</strong> " . $flower_type . "</p>";
        echo "<p><strong>Number of Flowers:</strong> " . $num_flowers . "</p>";
        echo "<p><strong>Container Type:</strong> " . $container_type_name . "</p>";
        echo "<p><strong>Container Color:</strong> " . $container_color_name . "</p>";
        echo "<hr>";
    }
} else {
    echo "No customization found.";
}
?>
