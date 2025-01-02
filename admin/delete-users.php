
<?php
$servername = "localhost";
$username = "root"; // Replace with your DB username
$password = ""; // Replace with your DB password
$dbname = "lasorpresa"; // Replace with your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php

    session_start();

    if(isset($_SESSION["user"])){
        if(($_SESSION["user"])=="" or $_SESSION['usertype']!='admin'){
            header("location: ../login.php");
        }

    }else{
        header("location: ../login.php");
    }
    
    
    if($_GET){
        //import database
        include("../config.php");
        $id=$_GET["id"];
        $result= $conn->query("select * from users where id=$id;");
        $email=($result->fetch_assoc())["email"];
        $sql= $conn->query("delete from users where email='$email';");
        //print_r($email);
        header("location: users.php");
    }


?>