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
        // Query the database to get the customer by email
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = email");
        $stmt->execute(['email' => $email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if the password matches
            if (password_verify($password, $user['password'])) {
                // Start the session and store user data
                $_SESSION['id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];

                // Redirect to the dashboard or user home page
                header("Location: dashboard.php"); // Change to the appropriate page
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
    <title>Admin Login</title>
</head>
<body>

<h2>Admin Login</h2>
<form action="login.php" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>

    <button type="submit" name="login">Login</button>
</form>

</body>
</html>
