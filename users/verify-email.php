<?php
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");


if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Verify the token
        $stmt = $pdo->prepare("SELECT cust_id FROM email_verifications WHERE token = :token");
        $stmt->execute([':token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $cust_id = $result['cust_id'];

            // Update the customer's status to active
            $stmt = $pdo->prepare("UPDATE customer SET cust_status = 'active' WHERE cust_id = :cust_id");
            $stmt->execute([':cust_id' => $cust_id]);

            // Remove the token from the database
            $stmt = $pdo->prepare("DELETE FROM email_verifications WHERE cust_id = :cust_id");
            $stmt->execute([':cust_id' => $cust_id]);

            echo "Email verified successfully!";
        } else {
            echo "Invalid or expired token.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No token provided.";
}
?>
