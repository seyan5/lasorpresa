<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $error = "An account with this email already exists.";
        } else {
            // Check if passwords match
            if ($password === $confirmPassword) {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert the new user into the database
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type) VALUES (:name, :email, :password, 'user')");
                $stmt->execute([':name' => $name, ':email' => $email, ':password' => $hashedPassword]);

                // Start session and store user data
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['user_type'] = 'user'; // Default user type

                // Redirect to login or home page
                header("Location: login.php");
                exit;
            } else {
                $error = "Passwords do not match.";
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../registerlogin.css">
    <title>Register</title>
</head>
<body>

    <div class="logo-container">
        <img src="../images/logo.png" alt="Logo" class="logo" />
    </div>

    <!-- Flower Image -->
    <div class="flower-container">
        <img src="../images/flower2.png" alt="Flower" class="flower" />
    </div>

    <h2>Register</h2>
    <form action="register.php" method="POST">
        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <div class="infield">
            <label for="name">Name:</label>
            <input placeholder="Full Name" type="text" id="name" name="name" required><br>
        </div>
        <div class="infield">
            <label for="email">Email:</label>
            <input placeholder="Email" type="email" id="email" name="email" required><br>
        </div>
        <div class="infield">
            <label for="password">Password:</label>
            <input placeholder="Password" type="password" id="password" name="password" required><br>
        </div>
        <div class="infield">
            <label for="confirm_password">Confirm Password:</label>
            <input placeholder="Confirm Password" type="password" id="confirm_password" name="confirm_password" required><br>
        </div>
        <p style="text-align: center; margin-top: 10px; font-size: 14px;">
            Already have an account? 
            <a href="login.php" style="color: #e18aaa; font-weight: bold; text-decoration: none;">Login</a>
        </p>
        <button type="submit" name="register">Register</button>
    </form>

</body>
</html>
