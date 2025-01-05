<?php
require_once('../header.php');

// Preventing direct access to the page
if (!isset($_REQUEST['id'])) {
    header('location: ../../logout.php');
    exit;
} else {
    // Check if the color ID is valid
    $statement = $pdo->prepare("SELECT * FROM color WHERE color_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    if ($total == 0) {
        header('location: logout.php');
        exit;
    }
}

// Delete color
$statement = $pdo->prepare("DELETE FROM colors WHERE color_id=?");
$statement->execute(array($_REQUEST['id']));

header('location: colors.php'); // Redirect back to the colors list page
?>
