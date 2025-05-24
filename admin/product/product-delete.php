<?php require_once('../header.php');
require_once '../auth.php';
?>

<?php
	// Getting photo ID to unlink from folder
	$statement = $pdo->prepare("SELECT * FROM product WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$featured_photo = $row['featured_photo'];
		unlink('../uploads/'.$featured_photo);
	}

	// Getting other photo ID to unlink from folder
	$statement = $pdo->prepare("SELECT * FROM product_photo WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$photo = $row['photo'];
		unlink('../uploads/product_photos/'.$photo);
	}


	// Delete from product
	$statement = $pdo->prepare("DELETE FROM product WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));

	// Delete from product_photo
	$statement = $pdo->prepare("DELETE FROM product_photo WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));

	// Delete from product_color
	$statement = $pdo->prepare("DELETE FROM product_color WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));

	// Delete from rating
	$statement = $pdo->prepare("DELETE FROM rating WHERE p_id=?");
	$statement->execute(array($_REQUEST['id']));

	// Delete from payment
	$statement = $pdo->prepare("SELECT * FROM `orders` WHERE order_id=?");  // Changed to correct column name (order_id or similar)
	$statement->execute(array($_REQUEST['id']));
	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
	foreach ($result as $row) {
		$statement1 = $pdo->prepare("DELETE FROM payment WHERE payment_id=?");
		$statement1->execute(array($row['payment_id']));
	}

	// Delete from orders
	$statement = $pdo->prepare("DELETE FROM `orders` WHERE order_id=?");  // Changed to correct column name (order_id or similar)
	$statement->execute(array($_REQUEST['id']));


	$_SESSION['delete_success'] = "Product deleted successfully";
	header('location: product.php');
	exit;
?>
