<?php
session_start();

if (isset($_SESSION['customization'])) {
    $customization = $_SESSION['customization'];
    echo '<h3>Your Floral Arrangement Customization</h3>';
    echo '<p>Flower Type: ' . htmlspecialchars(implode(', ', $customization['flower_type'])) . '</p>';
    echo '<p>Flower Color: ' . htmlspecialchars($customization['flower_color']) . '</p>';
    echo '<p>Number of Flowers: ' . htmlspecialchars($customization['num_flowers']) . '</p>';
    echo '<p>Container Type: ' . htmlspecialchars($customization['container_type']) . '</p>';
} else {
    echo "No customization found.";
}
?>
