<?php

include 'header.php';
require_once 'auth.php';

if (isset($_GET['cust_id'])) {
    // Debugging: Check the value of cust_id
    var_dump($_GET['cust_id']);
    $id = intval($_GET['cust_id']);
    
    // Proceed with the delete operation
    if ($stmt = $conn->prepare("DELETE FROM customer WHERE cust_id = ?")) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->close();
            $_SESSION['message'] = 'Customer deleted successfully!';
            header('Location: users.php');
            exit();
        } else {
            $stmt->close();
            $_SESSION['error'] = 'Error deleting customer: ' . $conn->error;
            header('Location: users.php');
            exit();
        }
    } else {
        die('Error preparing statement: ' . $conn->error);
    }
} else {
    // Debugging: No cust_id in the query string
    var_dump($_GET);
    die('Invalid request.');
}
?>
