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
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $usernameoremail, $usernameoremail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

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
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } elseif (empty($firstname) || empty($lastname) || empty($username)) {
            $error = "Please fill in all required fields.";
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    
            // Check if the username or email already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
            $stmt->bind_param("ss", $email, $username);
            $stmt->execute();
            $result = $stmt->get_result();
    
            if ($result && $result->num_rows > 0) {
                $error = "Username or email already exists.";
            } else {
                // Insert the user into the database
                $stmt = $conn->prepare("INSERT INTO users (name, username, email, contact, password, user_type) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssssss", $name, $username, $email, $contact, $passwordHash, $user_type);
                    if ($stmt->execute()) {
                        $success = "Registration successful!";
                    } else {
                        $error = "Error in registration: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $error = "Database error: " . $conn->error;
                }
            }
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
    <title>Sign in Sign up form</title>
    <!-- font awesome icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css"
        integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g =="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- css stylesheet -->
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
        <div class="form-container sign-up-container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                <h1>Create Account</h1>
                <span>or use your email for registration</span>
                <div class="infield">
                    <label for="name" class="form-label"></label>
                    <input type="text" name="firstname" placeholder="First Name" required>
                    <label></label>
                </div>
                <div class="infield">
                    <label for="name" class="form-label"></label>
                    <input type="text" name="lastname" placeholder="Last Name" required>
                    <label></label>
                </div>
                <div class="infield">
                    <label for="useremail" class="form-label"></label>
                    <input type="text" name="username" placeholder="Username" required>
                    <label></label>
                </div>
                <div class="infield">
                    <label for="email" class="form-label"></label>
                    <input type="email" placeholder="Email" name="email" required />
                    <label></label>
                </div>
                <div class="infield">
                    <label for="contact" class="form-label"></label>
                    <input type="text" name="contact" class="input-text" placeholder="Contact Number" required>
                    <label></label>
                </div>
                <div class="infield">
                    <input type="password" name="password" placeholder="Password" required>
                    <label></label>
                </div>
                <div class="infield">
                    <input type="password" name="confirmpassword" class="input-text" placeholder="Confirm Password" required>
                    <label></label>
                </div>
                <button type="submit" name="register">Sign Up</button>
                <?php if ($success) echo "<p class='success'>$success</p>"; ?>
                <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="#" method="POST" class="form-body">
                <h1>Sign in</h1>
                <div class="tulip-container">
                    <img src="images/flower1.png" alt="Flower" class="tulip" />
                </div>
                <div class="infield">
                    <label for="useremail" class="form-label"></label>
                    <input type="text" placeholder="Email or Username" name="usernameoremail" required>
                    <label></label>
                </div>
                <div class="infield">
                    <label for="password" class="form-label"></label>
                    <input type="password" placeholder="Password" name="password" required>
                    <label></label>
                </div>
                <a href="#" class="forgot">Forgot your password?</a>
                <button type="submit" name="login">Sign In</button>
                <?php if ($error) echo "<p class='error'>$error</p>"; ?>
            </form>
        </div>
        <div class="overlay-container" id="overlayCon">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info.</p>
                    <button id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start your journey with us.</p>
                    <button id="signUp">Sign Up</button>
                </div>
            </div>
            <button id="overlayBtn"></button>
        </div>
    </div>

    <!-- js code -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('container');
    const signInButton = document.getElementById('signIn');
    const signUpButton = document.getElementById('signUp');

    signInButton.addEventListener('click', () => {
        container.classList.remove('right-panel-active');
    });

    signUpButton.addEventListener('click', () => {
        container.classList.add('right-panel-active');
    });
});
      signUpButton.addEventListener('click', () => {
            container.classList.add('right-panel-active');
        });

        const signUpForm = document.querySelector('.sign-up-container form');
signUpForm.addEventListener('submit', (e) => {
    const password = document.querySelector('input[name="password"]').value;
    const confirmPassword = document.querySelector('input[name="confirmpassword"]').value;

    if (password !== confirmPassword) {
        e.preventDefault(); // Prevent form submission
        alert('Passwords do not match.');
    }
});
    </script>

</body>

</html>