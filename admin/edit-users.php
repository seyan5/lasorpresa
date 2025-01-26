<?php
include("../config.php");
require_once 'auth.php';
include('conn.php');



if ($_POST) {
    // Retrieve form data
    $cust_name = $_POST['cust_name'];
    $cust_email = $_POST["cust_email"];
    $cust_phone = $_POST["cust_phone"];
    $cust_address = $_POST["cust_address"];
    $cust_city = $_POST["cust_city"];
    $cust_zip = $_POST["cust_zip"];
    $cust_status = $_POST['cust_status'];
    $id = $_POST['cust_id00'];

    // SQL query to update user details and status
    $sql = "UPDATE customer 
            SET 
                cust_name = '$cust_name', 
                cust_email = '$cust_email', 
                cust_phone = '$cust_phone', 
                cust_address = '$cust_address', 
                cust_city = '$cust_city', 
                cust_zip = '$cust_zip', 
                cust_status = '$cust_status'
            WHERE cust_id = $id";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        header('Location: users.php'); // Redirect to users page after successful update
        exit();
    } else {
        die('Error updating record: ' . mysqli_error($conn)); // Output error if query fails
    }
}
?>
