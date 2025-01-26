<?php
require_once('conn.php');

// Check if the customer is logged in or not
if (!isset($_SESSION['customer'])) {
    header('location: ' . BASE_URL . 'logout.php');
    exit;
} else {
    // If customer is logged in, but admin makes them inactive, force logout
    $statement = $pdo->prepare("SELECT * FROM customer WHERE cust_id=? AND cust_status=?");
    $statement->execute(array($_SESSION['customer']['cust_id'], 0));
    $total = $statement->rowCount();
    if ($total) {
        header('location: ' . BASE_URL . 'logout.php');
        exit;
    }
}


if (isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';

    // Validate fields
    if (empty($_POST['cust_name'])) {
        $valid = 0;
        $error_message .= "Customer Name can't be Empty<br>";
    }

    if (empty($_POST['cust_phone'])) {
        $valid = 0;
        $error_message .= "Customer Phone can't be Empty<br>";
    }

    if (empty($_POST['cust_address'])) {
        $valid = 0;
        $error_message .= "Customer Address can't be Empty<br>";
    }

    if (empty($_POST['cust_city'])) {
        $valid = 0;
        $error_message .= "Customer City can't be Empty<br>";
    }

    if (empty($_POST['cust_zip'])) {
        $valid = 0;
        $error_message .= "Customer Zip can't be Empty<br>";
    }

    // If validation passes, update the profile
    if ($valid == 1) {
        $stmt = $pdo->prepare("UPDATE customer SET cust_name=?, cust_phone=?, cust_address=?, cust_city=?, cust_zip=? WHERE cust_id=?");
        $stmt->execute(array(
            strip_tags($_POST['cust_name']),
            strip_tags($_POST['cust_phone']),
            strip_tags($_POST['cust_address']),
            strip_tags($_POST['cust_city']),
            strip_tags($_POST['cust_zip']),
            $_SESSION['customer']['cust_id']
        ));

        // Fetch updated customer data and update session
        $statement = $pdo->prepare("SELECT * FROM customer WHERE cust_id = ?");
        $statement->execute([$_SESSION['customer']['cust_id']]);
        $updatedCustomer = $statement->fetch(PDO::FETCH_ASSOC);

        // Update session variables with updated customer data
        $_SESSION['customer']['cust_name'] = $updatedCustomer['cust_name'];
        $_SESSION['customer']['cust_phone'] = $updatedCustomer['cust_phone'];
        $_SESSION['customer']['cust_address'] = $updatedCustomer['cust_address'];
        $_SESSION['customer']['cust_city'] = $updatedCustomer['cust_city'];
        $_SESSION['customer']['cust_zip'] = $updatedCustomer['cust_zip'];

        $success_message = "Profile Information Updated successfully.";
    }
}
?>
<style>
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
    margin-top: 10rem; /* Adjust vertical spacing */
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
    background-color: #dd91ad;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

/* Hover effect */
.horizontal-table a:hover {
    background-color: #f75dd0;
    transform: scale(1.05); /* Slightly enlarge the link */
}

</style>
<?php include('navuser.php'); ?>
<link rel="stylesheet" href="../css/profileupd.css?">

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
                <div class="user-content">
                    <h3><?php echo "Update Profile"; ?></h3>
                    <?php
                    if (!empty($error_message)) {
                        echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>" . $error_message . "</div>";
                    }
                    if (!empty($success_message)) {
                        echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>" . $success_message . "</div>";
                    }
                    ?>
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="">Customer Name *</label>
                                <input type="text" class="form-control" name="cust_name" value="<?php echo isset($_SESSION['customer']['cust_name']) ? htmlspecialchars($_SESSION['customer']['cust_name']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Email Address *</label>
                                <input type="text" class="form-control" value="<?php echo isset($_SESSION['customer']['cust_email']) ? htmlspecialchars($_SESSION['customer']['cust_email']) : ''; ?>" disabled>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Phone Number *</label>
                                <input type="text" class="form-control" name="cust_phone" value="<?php echo isset($_SESSION['customer']['cust_phone']) ? htmlspecialchars($_SESSION['customer']['cust_phone']) : ''; ?>" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="">Address *</label>
                                <textarea name="cust_address" class="form-control" cols="30" rows="10" style="height:70px;" required><?php echo isset($_SESSION['customer']['cust_address']) ? htmlspecialchars($_SESSION['customer']['cust_address']) : ''; ?></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">City *</label>
                                <input type="text" class="form-control" name="cust_city" value="<?php echo isset($_SESSION['customer']['cust_city']) ? htmlspecialchars($_SESSION['customer']['cust_city']) : ''; ?>" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Zip Code *</label>
                                <input type="text" class="form-control" name="cust_zip" value="<?php echo isset($_SESSION['customer']['cust_zip']) ? htmlspecialchars($_SESSION['customer']['cust_zip']) : ''; ?>" required>
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Update" name="form1">
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
