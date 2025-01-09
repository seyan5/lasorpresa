<?php
session_start();

if (isset($_SESSION['customization'])) {
    $customization = $_SESSION['customization'];
    
    // Check if the flower_type is an array and not empty before imploding
    $flower_types = isset($customization['flower_type']) && is_array($customization['flower_type']) ? implode(', ', $customization['flower_type']) : 'Not selected';
    
    // Safely access other customization values
    $flower_color = isset($customization['flower_color']) ? htmlspecialchars($customization['flower_color']) : 'Not selected';
    $num_flowers = isset($customization['num_flowers']) ? htmlspecialchars($customization['num_flowers']) : 'Not selected';
    $container_type = isset($customization['container_type']) ? htmlspecialchars($customization['container_type']) : 'Not selected';
    
    echo '<h3>Your Floral Arrangement Customization</h3>';
    echo '<p>Flower Type: ' . $flower_types . '</p>';
    echo '<p>Flower Color: ' . $flower_color . '</p>';
    echo '<p>Number of Flowers: ' . $num_flowers . '</p>';
    echo '<p>Container Type: ' . $container_type . '</p>';
} else {
    echo "No customization found.";
}
?>
