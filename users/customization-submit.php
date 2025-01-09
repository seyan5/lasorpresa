<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the container and flower arrays
    $container_type = $_POST['container_type'];
    $flower_types = $_POST['flower_type'];
    $flower_colors = $_POST['flower_color'];
    $num_flowers = $_POST['num_flowers'];

    // Process each flower customization
    $customization_details = [];
    foreach ($flower_types as $index => $flower_type) {
        $customization_details[] = [
            'flower_type' => $flower_type,
            'flower_color' => $flower_colors[$index],
            'num_flowers' => $num_flowers[$index],
            'container_type' => $container_type
        ];
    }

    // Optionally, store this information in the session, database, or use for further processing
    $_SESSION['customization'] = $customization_details;

    // Redirect or show confirmation page
    header("Location: customization-view.php");
    exit;
} else {
    echo "Invalid request.";
}
?>
