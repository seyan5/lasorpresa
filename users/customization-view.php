<?php
session_start();

if (isset($_SESSION['customization']) && !empty($_SESSION['customization'])) {
    $customization = $_SESSION['customization'];

    echo '<h3>Your Floral Arrangement Customization</h3>';

    // Loop through each customization (for each flower)
    foreach ($customization as $index => $customize) {
        $flower_type = isset($customize['flower_type']) ? htmlspecialchars($customize['flower_type']) : 'Not selected';
        $num_flowers = isset($customize['num_flowers']) ? htmlspecialchars($customize['num_flowers']) : 'Not selected';
        $container_type = isset($customize['container_type']) ? htmlspecialchars($customize['container_type']) : 'Not selected';
        $container_color = isset($customize['container_color']) ? htmlspecialchars($customize['container_color']) : 'Not selected';

        echo "<p><strong>Flower Set " . ($index + 1) . ":</strong></p>";
        echo "<p><strong>Flower Type:</strong> " . $flower_type . "</p>";
        echo "<p><strong>Number of Flowers:</strong> " . $num_flowers . "</p>";
        echo "<p><strong>Container Type:</strong> " . $container_type . "</p>";
        echo "<p><strong>Container Color:</strong> " . $container_color . "</p>";
        echo "<hr>";
    }
} else {
    echo "No customization found.";
}
?>
