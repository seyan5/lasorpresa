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

<style>
:root {
    --pink: #e84393;
    --pink: #e84393;
    --main: #d0bcb3;
    --font: #d18276;
    --button: #d6a98f;
    --bg: rgb(233, 221, 204);
}

html, body {
    height: 100%; /* Ensure the body takes the full height of the viewport */
    margin: 0; /* Remove default margins */
    display: flex;
    justify-content: center; /* Horizontally center the content */
    align-items: center; /* Vertically center the content */
    background-color: #ffffff; /* Optional: Set a background color for the page */
}


.horizontal-table {
    display: flex;
    flex-wrap: wrap; /* Allows items to wrap on smaller screens */
    justify-content: center; /* Center items horizontally */
    align-items: center; /* Center items vertically */
    border-radius: 8px;
    margin: auto; /* Centers the container horiz    ontally */
    margin-top: -3rem; /* Adjust vertical spacing */
    width: fit-content; /* Adjust the width to fit content */
    margin-bottom: -5rem;
}

/* Style the links */
.horizontal-table a {
    display: block;
    padding: 10px 28px; /* Increase padding for larger clickable areas */
    margin: 10px; /* Add more spacing between items */
    text-align: center;
    font-size: 1.3rem; /* Make text larger */
    background-color: #333;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Hover effect */
.horizontal-table a:hover {
    background-color: var(--button);
    transform: scale(1.05); /* Slightly enlarge the link */
}

</style>
<?php include('navuser.php'); ?>
<link rel="stylesheet" href="../css/profileupd.css">

<section class="content">
            <div class="row">
                <div class="">
                    <div class="">
                        <div class="">
                            <table>
                                <thead>
                                </thead>
                                <tbody>
                                <div class="horizontal-table">
                                    <a href="customer-profile-update.php">Update Profile</a>
                                    <a href="customer-password-update.php">Update Password</a>
                                    <a href="customer-order.php">Orders</a>
                                    <a href="customize-view.php">Custom Orders</a>
                                </div>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

<div class="page">
    <div class="container">
        <div class="row">          
            <div class="col-md-12">
                <div class="user-content" style="width: 90rem !important;">
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
                                <div class="form-group" style="width: 90rem !important;">
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
