<?php
require_once('conn.php'); // Includes session_start and database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate required POST data
    if (
        !isset($_POST['container_type'], $_POST['container_color'], $_POST['flower_type'], $_POST['num_flowers'], $_POST['remarks'])
        || !is_array($_POST['flower_type'])
        || !is_array($_POST['num_flowers'])
        || !isset($_FILES['expected_image'])
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

    // Handle the uploaded image
    if ($_FILES['expected_image']['error'] == 0) {
        $targetDir = "uploads/";
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
    } else {
        echo "Image upload failed. Please try again.";
        exit;
    }

    // Save customization data to the database
    try {
        $pdo->beginTransaction();

        // Insert customization data
        $stmt = $pdo->prepare("INSERT INTO custom_orderitems (container_type, container_color, remarks) VALUES (?, ?, ?)");
        $stmt->execute([$container_type, $container_color, $remarks]);

        // Get the last inserted `order_id` to link the image
        $order_id = $pdo->lastInsertId();

        // Insert the uploaded image into the custom_images table
        $stmt = $pdo->prepare("INSERT INTO custom_images (order_id, expected_image) VALUES (?, ?)");
        $stmt->execute([$order_id, $fileName]);

        // Insert flower details
        foreach ($flower_types as $index => $flower_type) {
            $stmt = $pdo->prepare("INSERT INTO custom_orderitems (order_id, flower_type, num_flowers) VALUES (?, ?, ?)");
            $stmt->execute([$order_id, $flower_type, $num_flowers[$index]]);
        }

        $pdo->commit();
        echo "Customization submitted successfully!";
        header('Location: customize-cart.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    // Handle invalid request methods
    echo "Invalid request. Please use the customization form.";
    exit;
}
?>
