<?php
require_once('../header.php');

// Check if the ID is passed and valid
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('location: type.php');
    exit;
}

$type_id = $_GET['id'];

// Check if the type exists before deleting
$statement = $pdo->prepare("SELECT * FROM type WHERE type_id = ?");
$statement->execute([$type_id]);

if ($statement->rowCount() == 0) {
    // If no record found, redirect to the list page
    header('location: type.php');
    exit;
}

// Proceed with the deletion
$deleteStatement = $pdo->prepare("DELETE FROM type WHERE type_id = ?");
$deleteStatement->execute([$type_id]);

// Redirect back to the types list page
header('location: type.php');
exit;
?>