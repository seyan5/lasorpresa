<?php
session_start();
include 'config.php'; // Include your database connection file

date_default_timezone_set('Asia/Kolkata');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

$error = ''; // Initialize error variable
$success = ''; // Initialize success variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the form is for login or registration
    if (isset($_POST['login'])) {
        // Login functionality
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
                    header('Location: users/index.php');
                }
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "No user found with that email or username.";
        }
    } elseif (isset($_POST['register'])) {
        // Registration functionality
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
            $stmt = $conn->prepare("INSERT INTO users (name, username, email, contact, password, user_type) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die('prepare() failed: ' . htmlspecialchars($conn->error));
            }

            // Bind parameters
            $stmt->bind_param("ssssss", $name, $username, $email, $contact, $passwordHash, $user_type);

            // Execute statement
            if ($stmt->execute()) {
                $success = "Registration successful!";
            } else {
                $error = "Error: " . htmlspecialchars($stmt->error);
            }

            // Close statement
            $stmt->close();
        }
    }
}

// Close connection
$conn->close();
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
