<?php require_once('../header.php'); ?>

<?php
// Preventing the direct access of this page.
if(!isset($_REQUEST['id'])) {
	header('location: ../../logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM color WHERE color_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: ../../logout.php');
		exit;
	}
}
?>

<?php

	// Delete from color
	$statement = $pdo->prepare("DELETE FROM color WHERE color_id=?");
	$statement->execute(array($_REQUEST['id']));

	header('location: color.php');
?>