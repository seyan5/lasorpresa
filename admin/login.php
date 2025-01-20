<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        // Correct query to bind the :email parameter and include user_type
        $stmt = $pdo->prepare("SELECT id, name, email, password, user_type FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if the password matches
            if (password_verify($password, $user['password'])) {
                // Start the session and store user data
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];

                // Redirect based on user type
                if ($user['user_type'] === 'admin') {
                    header("Location: dashboard.php"); // Change to admin dashboard page
                } else {
                    header("Location: login.php"); // Change to user home page
                }
                exit;
            } else {
                echo "Incorrect password. Please try again.";
            }
        } else {
            echo "No account found with that email address.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../registerlogin.css">
    <title>Admin Login</title>
</head>
<body>

    <div class="logo-container">
        <img src="../images/logo.png" alt="Logo" class="logo" />
    </div>

    <!-- Flower Image -->
    <div class="flower-container">
        <img src="../images/flower2.png" alt="Flower" class="flower" />
    </div>

    <h2>Admin Login</h2>
    <form action="login.php" method="POST">
        <?php if (!empty($error)): ?>
            <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <div class="infield">
            <label for="cust_email">Email:</label>
            <input placeholder="Email" type="email" id="cust_email" name="cust_email" required><br>
        </div>
        <div class="infield">
            <label for="cust_password">Password:</label>
            <input placeholder="Password" type="password" id="cust_password" name="cust_password" required><br>
        </div>
        <p style="text-align: center; margin-top: 10px; font-size: 14px;">
            Don't Have an Account? 
            <a href="register.php" style="color: #e18aaa; font-weight: bold; text-decoration: none;">Register</a>
        </p>
        <button type="submit" name="login">Login</button>
    </form>

</body>
</html>

