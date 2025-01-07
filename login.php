<?php
ob_start();
session_start();
include("admin/inc/config.php");
include("admin/inc/functions.php");
include("admin/inc/CSRF_Protect.php");


if (isset($_POST['login'])) {
    $cust_email = $_POST['cust_email'];
    $cust_password = $_POST['cust_password'];

    try {
        // Query the database to get the customer by email
        $stmt = $pdo->prepare("SELECT cust_id, cust_name, cust_email, cust_password, cust_status FROM customer WHERE cust_email = :cust_email");
        $stmt->execute([':cust_email' => $cust_email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if the password matches
            if (password_verify($cust_password, $user['cust_password'])) {
                // Check if the user is active
                if ($user['cust_status'] === 'active') {
                    // Start the session and store user data
                    $_SESSION['cust_id'] = $user['cust_id'];
                    $_SESSION['cust_name'] = $user['cust_name'];
                    $_SESSION['cust_email'] = $user['cust_email'];

                    // Redirect to the dashboard or user home page
                    header("Location: users/index.php"); // Change to the appropriate page
                    exit;
                } else {
                    echo "Your account is not verified yet. Please check your email to verify your account.";
                }
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
    <title>Customer Login</title>
</head>
<body>

<h2>Login</h2>
<form action="login.php" method="POST">
    <label for="cust_email">Email:</label>
    <input type="email" id="cust_email" name="cust_email" required><br>

    <label for="cust_password">Password:</label>
    <input type="password" id="cust_password" name="cust_password" required><br>

    <button type="submit" name="login">Login</button>
</form>

</body>
</html>