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
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: users.php');
        exit();
    } else {
        die('Error deleting record: ' . $conn->error);
    }
    $stmt->close();
} else {
    die('Invalid request.');
}
?>