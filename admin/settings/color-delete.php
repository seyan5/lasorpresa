<?php
require_once('../header.php');

// Check if the ID is passed and valid
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('location: color.php');
    exit;
}

$color_id = $_GET['id'];

// Check if the color exists before deleting
$statement = $pdo->prepare("SELECT * FROM color WHERE color_id = ?");
$statement->execute([$color_id]);

if ($statement->rowCount() == 0) {
    // If no record found, redirect to the list page
    header('location: color.php');
    exit;
}

// Proceed with the deletion
$deleteStatement = $pdo->prepare("DELETE FROM color WHERE color_id = ?");
$deleteStatement->execute([$color_id]);

// Redirect back to the colors list page
header('location: color.php');
exit;
?>
