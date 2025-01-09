<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get arrays of selected flower customizations
    $flower_types = $_POST['flower_type'];
    $flower_colors = $_POST['flower_color'];
    $num_flowers = $_POST['num_flowers'];
    $container_types = $_POST['container_type'];

    // Process each flower customization
    $customization_details = [];
    foreach ($flower_types as $index => $flower_type) {
        $customization_details[] = [
            'flower_type' => $flower_type,
            'flower_color' => $flower_colors[$index],
            'num_flowers' => $num_flowers[$index],
            'container_type' => $container_types[$index]
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
