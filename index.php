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
    <title>Sign in Sign up Form</title>
    <!-- Font Awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- CSS stylesheet -->
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div class="logo-container">
        <img src="images/logo.png" alt="Logo" class="logo" />
    </div>

    <!-- Flower Image -->
    <div class="flower-container">
        <img src="images/flower2.png" alt="Flower" class="flower" />
    </div>

    <div class="container" id="container">
        < div class="form-container sign-up-container">
            <form method="POST" action="">
                <h1>Create Account</h1>
                <span>or use your email for registration</span>
                <div class="infield">
                    <input type="text" name="firstname" placeholder="First Name" required>
                </div>
                <div class="infield">
                    <input type="text" name="lastname" placeholder="Last Name" required>
                </div>
                <div class="infield">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="infield">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="infield">
                    <input type="text" name="contact" placeholder="Contact Number" required>
                </div>
                <div class="infield">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div class="infield">
                    <input type="password" name="confirmpassword" placeholder="Confirm Password" required>
                </div>
                <button type="submit" name="register">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form method="POST" action="">
                <h1>Sign in</h1>
                <div class="infield">
                    <input type="text" name="usernameoremail" placeholder="Username or Email" required>
                </div>
                <div class="infield">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <a href="#" class="forgot">Forgot your password?</a>
                <button type="submit" name="login">Sign In</button>
            </form>
        </div>
        <div class="overlay-container" id="overlayCon">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your Personal Info!</p>
                    <button id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us</p>
                    <button id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript code -->
    <script>
        const container = document.getElementById('container');
        const overlayBtn = document.getElementById('overlayBtn');

        document.getElementById('signIn').addEventListener('click', () => {
            container.classList.remove('right-panel-active');
        });

        document.getElementById('signUp').addEventListener('click', () => {
            container.classList.add('right-panel-active');
        });
    </script>

</body>

</html>