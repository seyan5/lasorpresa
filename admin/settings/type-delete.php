<?php
require_once('../header.php');

// Check if an ID is passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to the types list page if no ID is provided
    header('location: type.php');
    exit;
}

$type_id = $_GET['id'];

// Check if the type exists
$statement = $pdo->prepare("SELECT * FROM type WHERE type_id = ?");
$statement->execute([$type_id]);

// If no record found with the given ID
if ($statement->rowCount() == 0) {
    // Redirect to the types list page if the record doesn't exist
    header('location: type.php');
    exit;
}

// Delete the record from the type table
$deleteStatement = $pdo->prepare("DELETE FROM type WHERE type_id = ?");
$deleteStatement->execute([$type_id]);

// Redirect back to the types list page after deletion
header('location: type.php');
exit;
?>
