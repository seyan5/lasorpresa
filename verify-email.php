<?php
require 'users/header.php'; // Include your database connection here

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Find the token in the email_verifications table
    $stmt = $pdo->prepare("SELECT * FROM email_verifications WHERE token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $verification = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($verification) {
        // Token is valid, update the customer status to active
        $cust_id = $verification['cust_id'];
        $update_stmt = $pdo->prepare("UPDATE customer SET cust_status = 'active' WHERE cust_id = :cust_id");
        $update_stmt->bindParam(':cust_id', $cust_id);
        $update_stmt->execute();

        // Delete the verification token (optional)
        $delete_stmt = $pdo->prepare("DELETE FROM email_verifications WHERE token = :token");
        $delete_stmt->bindParam(':token', $token);
        $delete_stmt->execute();

        echo "Your email has been verified. You can now log in.";
    } else {
        echo "Invalid or expired verification token.";
    }
}
?>
