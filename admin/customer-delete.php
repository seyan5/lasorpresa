<?php
include("header.php");
require_once 'auth.php';
 // Make sure config.php is properly included



if (isset($_POST['cust_id'])) {
    $cust_id = $_POST['cust_id'];

    // First, delete the customer record from the email verification table
    $stmt = $pdo->prepare("DELETE FROM email_verifications WHERE cust_id = :cust_id");
    $stmt->bindParam(':cust_id', $cust_id);
    $stmt->execute();

    // Then, delete the customer from the customer table
    $stmt = $pdo->prepare("DELETE FROM customer WHERE cust_id = :cust_id");
    $stmt->bindParam(':cust_id', $cust_id);
    if ($stmt->execute()) {
        // Redirect back to the customer management page
        header("Location: customer.php");
        exit;
    } else {
        echo "Error deleting customer.";
    }
}
?>
