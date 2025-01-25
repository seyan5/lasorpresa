<?php
require_once('conn.php'); // Includes session_start and database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        !isset($_POST['container_type'], $_POST['container_color'], $_POST['flower_type'], $_POST['num_flowers'], $_POST['remarks'])
        || !is_array($_POST['flower_type'])
        || !is_array($_POST['num_flowers'])
    ) {
        echo "Invalid customization data. Please try again.";
        header("refresh:3;url=customization.php");
        exit;
    }

    $container_type = htmlspecialchars($_POST['container_type']);
    $container_color = htmlspecialchars($_POST['container_color']);
    $flower_types = array_map('htmlspecialchars', $_POST['flower_type']);
    $num_flowers = array_map('intval', $_POST['num_flowers']);
    $remarks = htmlspecialchars($_POST['remarks']);

    $expected_image = null;
    if (isset($_FILES['expected_image']) && $_FILES['expected_image']['error'] == 0) {
        $targetDir = "uploads/";
        $fileName = time() . '_' . basename($_FILES['expected_image']['name']);
        $targetFilePath = $targetDir . $fileName;

        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES['expected_image']['tmp_name'], $targetFilePath)) {
                $expected_image = $fileName;
            }
        }
    }

    $customization_details = [
        'container_type' => $container_type,
        'container_color' => $container_color,
        'remarks' => $remarks,
        'expected_image' => $expected_image,
        'flowers' => []
    ];

    foreach ($flower_types as $index => $flower_type) {
        $customization_details['flowers'][] = [
            'flower_type' => $flower_type,
            'num_flowers' => $num_flowers[$index]
        ];
    }

    $_SESSION['customization'][] = $customization_details;
    header('Location: customize-cart.php');
    exit;
}
?>
