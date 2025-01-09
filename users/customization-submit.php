<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and get form data
    $flower_type = htmlspecialchars($_POST['flower_type']);
    $flower_color = htmlspecialchars($_POST['flower_color']);
    $num_flowers = (int)$_POST['num_flowers'];
    $container_type = htmlspecialchars($_POST['container_type']);

    // Store the customization in the session (or database)
    $_SESSION['customization'] = [
        'flower_type' => $flower_type,
        'flower_color' => $flower_color,
        'num_flowers' => $num_flowers,
        'container_type' => $container_type
    ];

    // Optionally, redirect to a cart or summary page
    header("Location: customization-view.php");
    exit;
} else {
    echo "Invalid request.";
}
