<?php
session_start();
include 'config.php'; // File where you define your database connection
date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d');

    $_SESSION["date"]=$date;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usernameoremail = $_POST['usernameoremail'];
    $password = $_POST['password'];

    // Fetch user from the database
    $sql = "SELECT * FROM users WHERE email = '$usernameoremail' OR username = '$usernameoremail'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];

            if ($user['user_type'] == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: users/home.php');
            }
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email or username.";
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
    <link rel="stylesheet" href="css/login.css">
        
    <title>Login</title>
</head>
<body>

    <div class="container">
        <form action="" method="POST" class="form-body">
            <div class="header">
                <p class="header-text">Welcome Back!</p>
                <p class="sub-text">Login with your details to continue</p>
            </div>

            <div class="input-group">
                <label for="useremail" class="form-label">Username or Email:</label>
                <input type="text" name="usernameoremail" class="input-text" placeholder="Username/Email" required>
            </div>

            <div class="input-group">
                <label for="password" class="form-label">Password:</label>
                <input type="password" name="password" class="input-text" placeholder="Password" required>
            </div>

            <div class="button-group">
                <input type="submit" value="Login" class="login-btn btn-primary btn">
            </div>

            <div class="footer">
                <p class="sub-text" style="font-weight: 280;">Don't have an account? 
                    <a href="register.php" class="hover-link1 non-style-link">Sign Up</a>
                </p>
            </div>
        </form>
    </div>

</body>
</html>
