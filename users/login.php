<?php
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");

if (isset($_POST['login'])) {
    $cust_email = $_POST['cust_email'];
    $cust_password = $_POST['cust_password'];

    try {
        // Query the database to get the customer by email
        $stmt = $pdo->prepare("SELECT cust_id, cust_name, cust_email, cust_password, cust_phone, cust_address, cust_city, cust_zip, cust_status FROM customer WHERE cust_email = :cust_email");
        $stmt->execute([':cust_email' => $cust_email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if the password matches
            if (password_verify($cust_password, $user['cust_password'])) {
                // Check if the user is active
                if ($user['cust_status'] === 'active') {
                    // Store user data in the session
                    $_SESSION['customer'] = [
                        'cust_id' => $user['cust_id'],
                        'cust_name' => $user['cust_name'],
                        'cust_email' => $user['cust_email'],
                        'cust_phone' => $user['cust_phone'],
                        'cust_address' => $user['cust_address'],
                        'cust_city'  => $user['cust_city'],
                        'cust_zip'   => $user['cust_zip'],
                    ];

                    // Debugging: Log session data after login
                    file_put_contents('debug.log', "Login session set: " . print_r($_SESSION, true), FILE_APPEND);

                    // Redirect to the home page or dashboard
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Your account is not verified yet. Please check your email to verify your account.";
                }
            } else {
                $error = "Incorrect password. Please try again.";
            }
        } else {
            $error = "No account found with that email address.";
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
    <title>Customer Login</title>
</head>
<body>

    <div class="logo-container">
        <img src="../images/logo.png" alt="Logo" class="logo" />
    </div>

    <!-- Flower Image -->
    <div class="flower-container">
        <img src="../images/flower2.png" alt="Flower" class="flower" />
    </div>

    <h2>Login</h2>
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
