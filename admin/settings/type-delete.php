<?php require_once('../header.php'); ?>

<?php
// Preventing the direct access of this page.
if(!isset($_REQUEST['id'])) {
	header('location: ../../logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM type WHERE type_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
?>

<?php

	// Delete from type
	$statement = $pdo->prepare("DELETE FROM type WHERE type_id=?");
	$statement->execute(array($_REQUEST['id']));

	header('location: type.php');
?>