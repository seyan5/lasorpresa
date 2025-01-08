<?php
    include("../config.php");
    
    if($_POST){
        $result= $conn->query("SELECT * from customer");
        $cust_name=$_POST['cust_name'];
        $cust_email=$_POST["cust_email"];
        $cust_phone = $_POST["cust_phone"];
        $cust_address = $_POST["cust_address"];
        $cust_city = $_POST["cust_city"];
        $cust_zip = $_POST["cust_zip"];
        $cust_status = $_POST['cust_status'];
        $id=$_POST['cust_id00'];

        // Corrected SQL query
        $sql = "UPDATE customer SET cust_name='$cust_name', cust_email='$cust_email', cust_phone='$cust_phone', cust_address='$cust_address', cust_city='$cust_city', cust_zip='$cust_zip' WHERE cust_id=$id";
        $result = mysqli_query($conn, $sql);
    
        if ($result) {
            header('Location: users.php');
            exit();
        } else {
            die('Error updating record: ' . mysqli_error($conn));
        }
    }
?>

</body>
</html>