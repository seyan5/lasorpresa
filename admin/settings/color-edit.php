<?php
include("../header.php");
require_once '../auth.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['color_id'], $_POST['color_name'])) {
    $colorId = $_POST['color_id'];
    $colorName = $_POST['color_name'];

    // Validate input
    if (empty($colorName)) {
        echo json_encode(['error' => 'Color Name cannot be empty']);
        exit;
    }

    try {
        // Update color in the database
        $stmt = $pdo->prepare("UPDATE color SET color_name = :color_name WHERE color_id = :color_id");
        $stmt->execute([':color_name' => $colorName, ':color_id' => $colorId]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => 'Color updated successfully!']);
        } else {
            echo json_encode(['error' => 'No changes made.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
}
