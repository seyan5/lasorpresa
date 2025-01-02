<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

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
