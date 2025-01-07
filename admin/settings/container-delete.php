<?php
require_once('../header.php');

// Preventing direct access to the page
if (!isset($_REQUEST['id'])) {
    header('location: ../logout.php');
    exit;
} else {
    // Check if the container ID is valid
    $statement = $pdo->prepare("SELECT * FROM container WHERE container_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    if ($total == 0) {
        header('location: ../logout.php');
        exit;
    }
}

// Delete container
$statement = $pdo->prepare("DELETE FROM container WHERE container_id=?");
$statement->execute(array($_REQUEST['id']));

header('location: container.php'); // Redirect back to the containers list page
?>
