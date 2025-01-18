<?php
require_once('conn.php'); // Includes session_start and database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required POST data
    if (
        !isset($_POST['container_type'], $_POST['container_color'], $_POST['flower_type'], $_POST['num_flowers'], $_POST['remarks'], $_FILES['expected_image'])
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
    $total_price = 0; // Initialize total price

    // Ensure flower types and quantities match in count
    if (count($flower_types) !== count($num_flowers)) {
        echo "Flower type and quantity mismatch. Please try again.";
        exit;
    }

    // Handle the uploaded image
    $uploaded_image_name = null;
    if ($_FILES['expected_image']['error'] == 0) {
        $targetDir = "../uploads/custom_images/";
        $fileName = basename($_FILES['expected_image']['name']);
        $targetFilePath = $targetDir . $fileName;

        // Validate file type
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            echo "Invalid file type. Please upload an image file (jpg, jpeg, png, gif).";
            exit;
        }

        // Move the file to the server
        if (!move_uploaded_file($_FILES['expected_image']['tmp_name'], $targetFilePath)) {
            echo "Error uploading the image. Please try again.";
            exit;
        }
        $uploaded_image_name = $fileName;
    } else {
        echo "Image upload failed. Please try again.";
        exit;
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
?>
