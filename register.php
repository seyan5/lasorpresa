<?php
session_start();
include 'config.php'; // Include your database connection file

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');

$_SESSION["date"]=$date;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    

    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $name = $firstname . " " . $lastname;
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $password = $_POST['password'];
    $confirmpassword = $_POST['confirmpassword'];
    $user_type = 'user';

    
    // Basic validation
    if ($password !== $confirmpassword) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO users (name, username, email, contact, password, user_type) VALUES ('$name', '$username', '$email', '$contact', '$passwordHash', '$user_type')");
        if ($stmt === false) {
            die('prepare() failed: ' . htmlspecialchars($conn->error));
        }

     //   $stmt->bind_param($firstname, $lastname, $email, $contact, $passwordHash, $user_type);

        // Execute statement
        if ($stmt->execute()) {
            echo
            "<script> alert('Registration successful')</script>";
            $success = "Registration successful!";
        } else {
            $error = "Error: " . htmlspecialchars($stmt->error);
        }

        // Close statement and connection
        $stmt->close();
        $conn->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
        
    <title>Sign Up</title>
</head>
<body>
    <div class="container">
        <form action="" method="POST">
            <div class="header">
                <p class="header-text">Let's Get Started</p>
                <p class="sub-text">Add Your Personal Details to Continue</p>
            </div>

            <div class="input-group">
                <label for="name" class="form-label">Name:</label>
                <input type="text" name="firstname" class="input-text" placeholder="First Name" required>
                <input type="text" name="lastname" class="input-text" placeholder="Last Name" required>
            </div>

            <div class="input-group">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" class="input-text" placeholder="Email" required>
            </div>

            <div class="input-group">
                <label for="username" class="form-label">Username:</label>
                <input type="text" name="username" class="input-text" placeholder="Username" required>
            </div>

            <div class="input-group">
                <label for="contact" class="form-label">Contact:</label>
                <input type="text" name="contact" class="input-text" placeholder="Contact Number" required>
            </div>
            <div class="input-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" class="input-text" placeholder="Password" required>
                <input type="password" name="confirmpassword" class="input-text" placeholder="Confirm Password" required>
            </div>

            <div class="button-group">
                <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                <input type="submit" value="Next" class="login-btn btn-primary btn">
            </div>

            <div class="footer">
                <p class="sub-text" style="font-weight: 280;">Already have an account? 
                    <a href="login.php" class="hover-link1 non-style-link">Login</a>
                </p>
            </div>
        </form>
    </div>

</body>
</html>
