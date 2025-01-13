<?php require_once('conn.php'); ?>

<?php
// Check if the customer is logged in or not
if(!isset($_SESSION['customer'])) {
    header('location: '.BASE_URL.'logout.php');
    exit;
} else {
    // If customer is logged in, but admin make him inactive, then force logout this user.
    $statement = $pdo->prepare("SELECT * FROM customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'],0));
    $total = $statement->rowCount();
    if($total) {
        header('location: '.BASE_URL.'logout.php');
        exit;
    }
}
?>

<?php
if (isset($_POST['form1'])) {

    $valid = 1;

    if( empty($_POST['cust_password']) || empty($_POST['cust_re_password']) ) {
        $valid = 0;
        $error_message .= "Password can not be Empty"."<br>";
    }

    if( !empty($_POST['cust_password']) && !empty($_POST['cust_re_password']) ) {
        if($_POST['cust_password'] != $_POST['cust_re_password']) {
            $valid = 0;
            $error_message .= "Password do not Match"."<br>";
        }
    }
    
    if ($valid == 1) {

        // Get the password from the form
        $password = strip_tags($_POST['cust_password']);
    
        // Hash the password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
        // Update the password in the database
        $statement = $pdo->prepare("UPDATE customer SET cust_password=? WHERE cust_id=?");
        $statement->execute(array($hashed_password, $_SESSION['customer']['cust_id']));
    
        // Clear the session password to force the next login to re-check the password
        unset($_SESSION['customer']['cust_password']);
    
        // Update the session password with the new hashed password
        $_SESSION['customer']['cust_password'] = $hashed_password;
    
        // Display success message
        $success_message = "Password is updated successfully";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/navhead.css">
    <link rel="stylesheet" href="../css/profileupd.css">
    <title>Navbar Fix</title>
</head>
<body>
<header>
    <img src="../images/logo.png" alt="Logo" class="logos">
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="customer-profile-update.php">Update Profile</a>
        <a href="customer-password-update.php">Update Password</a>
        <a href="customer-order.php">Orders</a>
        <a href="customize-view.php">Custom Orders</a>
    </nav>
    <div class="icons">
    <a href="shopcart.php" class="fas fa-shopping-cart"></a>
    <div class="user-dropdown">
        <a href="#" class="fas fa-user" onclick="toggleDropdown()"></a>
        <div class="dropdown-menu" id="userDropdown">
            <?php if (isset($_SESSION['customer'])): ?>
                <p>Welcome, <?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?></p>
                <hr>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</header>

<div class="page">
    <div class="container">
        <div class="row">          
            <div class="col-md-12">
                <div class="user-content">
                    <h3 class="text-center">
                        <?php echo "Update Password"; ?>
                    </h3>
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <?php
                                if($error_message != '') {
                                    echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                                }
                                if($success_message != '') {
                                    echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                                }
                                ?>
                                <div class="form-group">
                                    <label for=""><?php echo "New Password"; ?> *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>
                                <div class="form-group">
                                    <label for=""><?php echo "Retype New Password"; ?> *</label>
                                    <input type="password" class="form-control" name="cust_re_password">
                                </div>
                                <input type="submit" class="btn btn-primary" value="<?php echo "Update"; ?>" name="form1">
                            </div>
                        </div>
                        
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>
