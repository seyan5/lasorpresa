
<?php
    include("../config.php");
    if($_POST){
        //print_r($_POST);
        $result= $conn->query("select * from users");
        $name=$_POST['name'];
        $oldemail=$_POST["oldemail"];
        $contact=$_POST['contact'];
        $email=$_POST['email'];
        $id=$_POST['id00'];
        

        $sql = "UPDATE users SET name='$name', email='$email', contact='$contact', WHERE id=$id";    
        $result = mysqli_query($conn, $sql);
    
        if ($result) {
            // Redirect to users.php after updating
            header('Location: users.php');
            exit();
        } else {
            die('Error updating record: ' . mysqli_error($conn));
        }
    }
    ?>
    
</body>
</html>