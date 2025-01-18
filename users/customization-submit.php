<?php
require_once('conn.php'); // Includes session_start and database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required POST data
    if (
        !isset($_POST['container_type'], $_POST['container_color'], $_POST['flower_type'], $_POST['num_flowers'], $_POST['remarks'])
        || !is_array($_POST['flower_type'])
        || !is_array($_POST['num_flowers'])
    ) {
        echo "Incomplete or invalid customization data. Please go back and try again.";
        exit;
    }

    // Sanitize and retrieve inputs
    $container_type = htmlspecialchars($_POST['container_type']);
    $container_color = htmlspecialchars($_POST['container_color']);
    $flower_types = array_map('htmlspecialchars', $_POST['flower_type']);
    $num_flowers = array_map('intval', $_POST['num_flowers']);
    $remarks = htmlspecialchars($_POST['remarks']);

    // Ensure flower types and quantities match in count
    if (count($flower_types) !== count($num_flowers)) {
        echo "Flower type and quantity mismatch. Please try again.";
        exit;
    }

    // Prepare customization details
    $customization_details = [];
    foreach ($flower_types as $index => $flower_type) {
        $customization_details[] = [
            'flower_type' => $flower_type,
            'num_flowers' => $num_flowers[$index],
            'container_type' => $container_type,
            'container_color' => $container_color,
            'remarks' => $remarks
        ];
    }

    // Store customization in session
    $_SESSION['customization'] = $customization_details;

    // Redirect to customization-cart.php
    header('Location: customize-cart.php');
    exit;
} else {
    // Handle invalid request methods
    echo "Invalid request. Please use the customization form.";
    exit;
}
?>
