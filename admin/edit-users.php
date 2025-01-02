
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
    
    

    //import database
    include("../config.php");



    if($_POST){
        //print_r($_POST);
        $result= $conn->query("select * from users");
        $name=$_POST['name'];
        $oldemail=$_POST["oldemail"];
        $contact=$_POST['contact'];
        $email=$_POST['email'];
        $id=$_POST['id'];
        

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