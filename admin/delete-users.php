<?php
session_start();
// Include database configuration
include '../config.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle Delete
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare and execute the delete query
    if ($stmt = $conn->prepare("DELETE FROM users WHERE id = ?")) {
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $stmt->close();
            header('Location: users.php');
            exit();
        } else {
            $stmt->close();
            die('Error deleting record: ' . $conn->error);
        }
    } else {
        die('Error preparing statement: ' . $conn->error);
    }
} else {
    die('Invalid request.');
}
?>
