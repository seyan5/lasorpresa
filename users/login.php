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
                    // Store user data in the session under 'customer'
                    $_SESSION['customer'] = [
                        'cust_id' => $user['cust_id'],
                        'cust_name' => $user['cust_name'],
                        'cust_email' => $user['cust_email'],
                        'cust_phone' => $user['cust_phone'],
                        'cust_address' => $user['cust_address'],
                        'cust_city'  => $user['cust_city'],
                        'cust_zip'   => $user['cust_zip'],
                    ];

                    // Redirect to the home page or dashboard
                    header("Location: index.php");
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
