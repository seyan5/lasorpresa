<?php
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Verify the token
        $stmt = $pdo->prepare("SELECT cust_id FROM email_verifications WHERE token = :token");
        $stmt->execute([':token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $cust_id = $result['cust_id'];

            // Update the customer's status to active
            $stmt = $pdo->prepare("UPDATE customer SET cust_status = 'active' WHERE cust_id = :cust_id");
            $stmt->execute([':cust_id' => $cust_id]);

            // Remove the token from the database
            $stmt = $pdo->prepare("DELETE FROM email_verifications WHERE cust_id = :cust_id");
            $stmt->execute([':cust_id' => $cust_id]);

            $message = "Email verified successfully!";
        } else {
            $message = "Invalid or expired token.";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
} else {
    $message = "No token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        .container h2 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #34495e;
            margin-bottom: 20px;
        }
        .icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        .btn {
            background-color: #e18aaa;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #c9778f;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <h2>Email Verification</h2>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>

        <a href="login.php" class="btn">Login?</a>
    </div>

</body>
</html>
