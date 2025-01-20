<?php require_once('../header.php');
require_once '../auth.php';
?>

<?php
// Preventing the direct access of this page.
if(!isset($_REQUEST['id'])) {
	header('location: logout.php');
	exit;
} else {
	// Check the id is valid or not
	$statement = $pdo->prepare("SELECT * FROM top_category WHERE tcat_id=?");
	$statement->execute(array($_REQUEST['id']));
	$total = $statement->rowCount();
	if( $total == 0 ) {
		header('location: ../../logout.php');
		exit;
	}
}
?>

<?php	
	$statement = $pdo->prepare("SELECT * 
							FROM top_category t1
							JOIN mid_category t2
							ON t1.tcat_id = t2.tcat_id
							JOIN end_category t3
							ON t2.mcat_id = t3.mcat_id
							WHERE t1.tcat_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$ecat_ids[] = $row['ecat_id'];
	}

	if(isset($ecat_ids)) {

		for($i=0;$i<count($ecat_ids);$i++) {
			$statement = $pdo->prepare("SELECT * FROM product WHERE ecat_id=?");
			$statement->execute(array($ecat_ids[$i]));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
			foreach ($result as $row) {
				$p_ids[] = $row['p_id'];
			}
		}

		for($i=0;$i<count($p_ids);$i++) {

			// Getting photo ID to unlink from folder
			$statement = $pdo->prepare("SELECT * FROM product WHERE p_id=?");
			$statement->execute(array($p_ids[$i]));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
			foreach ($result as $row) {
				$p_featured_photo = $row['p_featured_photo'];
				unlink('../assets/uploads/'.$p_featured_photo);
			}

			// Getting other photo ID to unlink from folder
			$statement = $pdo->prepare("SELECT * FROM product_photo WHERE p_id=?");
			$statement->execute(array($p_ids[$i]));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
			foreach ($result as $row) {
				$photo = $row['photo'];
				unlink('../assets/uploads/product_photo/'.$photo);
			}

			// Delete from photo
			$statement = $pdo->prepare("DELETE FROM product WHERE p_id=?");
			$statement->execute(array($p_ids[$i]));

			// Delete from product_photo
			$statement = $pdo->prepare("DELETE FROM product_photo WHERE p_id=?");
			$statement->execute(array($p_ids[$i]));

			// Delete from product_size
			$statement = $pdo->prepare("DELETE FROM product_size WHERE p_id=?");
			$statement->execute(array($p_ids[$i]));

			// Delete from product_color
			$statement = $pdo->prepare("DELETE FROM product_color WHERE p_id=?");
			$statement->execute(array($p_ids[$i]));

			// Delete from rating
			$statement = $pdo->prepare("DELETE FROM rating WHERE p_id=?");
			$statement->execute(array($p_ids[$i]));

			// Delete from payment
			$statement = $pdo->prepare("SELECT * FROM order WHERE product_id=?");
			$statement->execute(array($p_ids[$i]));
			$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
			foreach ($result as $row) {
				$statement1 = $pdo->prepare("DELETE FROM payment WHERE payment_id=?");
				$statement1->execute(array($row['payment_id']));
			}

			// Delete from order
			$statement = $pdo->prepare("DELETE FROM order WHERE product_id=?");
			$statement->execute(array($p_ids[$i]));
		}

		// Delete from end_category
		for($i=0;$i<count($ecat_ids);$i++) {
			$statement = $pdo->prepare("DELETE FROM end_category WHERE ecat_id=?");
			$statement->execute(array($ecat_ids[$i]));
		}

	}

	// Delete from mid_category
	$statement = $pdo->prepare("DELETE FROM mid_category WHERE tcat_id=?");
	$statement->execute(array($_REQUEST['id']));

	// Delete from top_category
	$statement = $pdo->prepare("DELETE FROM top_category WHERE tcat_id=?");
	$statement->execute(array($_REQUEST['id']));

	header('location: topcategory.php');
?>