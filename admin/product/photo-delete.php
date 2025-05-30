<?php
require_once('../header.php');
include('../auth.php');


if(!isset($_REQUEST['id'])) {
	header('location: ../logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM photo WHERE id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: logout.php');
		exit;
	}
}
	
// Getting photo ID to unlink from folder
$statement = $pdo->prepare("SELECT * FROM photo WHERE id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$photo = $row['photo'];
}

// Unlink the photo
if($photo!='') {
	unlink('../uploads/'.$photo);
}

// Delete from tbl_photo
$statement = $pdo->prepare("DELETE FROM photo WHERE id=?");
$statement->execute(array($_REQUEST['id']));

header('location: photo.php');
?>