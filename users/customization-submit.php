<?php

require_once('conn.php'); // Include DB connection or additional setup

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required POST data
    if (!isset($_POST['container_type'], $_POST['container_color'], $_POST['flower_type'], $_POST['num_flowers'], $_POST['remarks'])) {
        echo "Incomplete customization data. Please go back and try again.";
        exit;
    }

    // Sanitize and retrieve inputs
    $container_type = htmlspecialchars($_POST['container_type']);
    $container_color = htmlspecialchars($_POST['container_color']);
    $flower_types = $_POST['flower_type'];
    $num_flowers = $_POST['num_flowers'];
    $remarks = htmlspecialchars($_POST['remarks']);

    // Ensure inputs are arrays and match in count
    if (!is_array($flower_types) || !is_array($num_flowers) || count($flower_types) !== count($num_flowers)) {
        echo "Invalid flower selection data. Please try again.";
        exit;
    }

    // Prepare customization details
    $customization_details = [];
    foreach ($flower_types as $index => $flower_type) {
        $customization_details[] = [
            'flower_type' => htmlspecialchars($flower_type),
            'num_flowers' => (int) $num_flowers[$index],
            'container_type' => $container_type,
            'container_color' => $container_color,
            'remarks' => $remarks // Include remarks in customization details
        ];
    }

    // Store customization in session
    $_SESSION['customization'] = $customization_details;

    // Redirect to confirmation page
    header('Location: customize-cart.php');
    exit;
} else {
    echo "Invalid request. Please use the customization form.";
}
?>