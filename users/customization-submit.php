<?php
session_start();
require_once('header.php'); // Make sure to include your DB connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the customized data from the POST request
    $container_type = $_POST['container_type'];
    $container_color = $_POST['container_color'];
    $flower_types = $_POST['flower_type'];
    $num_flowers = $_POST['num_flowers'];

    // Optionally, process the customization (store in session or DB)
    $customization_details = [];
    foreach ($flower_types as $index => $flower_type) {
        $customization_details[] = [
            'flower_type' => $flower_type,
            'num_flowers' => $num_flowers[$index],
            'container_type' => $container_type,
            'container_color' => $container_color
        ];
    }

    // Store the customization in session for later use
    $_SESSION['customization'] = $customization_details;

    // Redirect the user to the confirmation page
    header('Location: customize-checkout.php');
    exit;
} else {
    echo "Invalid request.";
}
?>
