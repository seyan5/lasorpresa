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