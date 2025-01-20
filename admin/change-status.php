<?php

include("header.php");
require_once 'auth.php';
 // Make sure config.php is properly included


if (isset($_POST['cust_id']) && isset($_POST['status'])) {
    $cust_id = $_POST['cust_id'];
    $status = $_POST['status'];

    // Update the customer status in the database
    $stmt = $pdo->prepare("UPDATE customer SET cust_status = :status WHERE cust_id = :cust_id");
    $stmt->bindParam(':cust_id', $cust_id);
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        // Redirect back to the customer management page
        header("Location: users.php");
        exit;
    } else {
        echo "Error updating status.";
    }
}
?>
