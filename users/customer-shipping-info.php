<?php require_once('header.php'); ?>

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

    if(empty($_POST['cust_s_name'])) {
        $valid = 0;
        $error_message .= "Customer Name cant be Empty"."<br>";
    }

    if(empty($_POST['cust_s_phone'])) {
        $valid = 0;
        $error_message .= "Customer Phone cant be Empty"."<br>";
    }

    if(empty($_POST['cust_s_address'])) {
        $valid = 0;
        $error_message .= "Customer Address cant be Empty"."<br>";
    }


    if(empty($_POST['cust_s_city'])) {
        $valid = 0;
        $error_message .= "Customer City cant be Empty"."<br>";
    }


    if(empty($_POST['cust_s_zip'])) {
        $valid = 0;
        $error_message .= "Customer Zip cant be Empty"."<br>";
    }

    if ($valid == 1) {
       
        $statement = $pdo->prepare("UPDATE customer SET cust_s_name=?, cust_s_phone=?, cust_s_address=?, cust_s_city=?, cust_s_zip=? WHERE cust_id=?");
        $statement->execute(array(
            strip_tags($_POST['cust_s_name']),
            strip_tags($_POST['cust_s_phone']),
            strip_tags($_POST['cust_s_address']),
            strip_tags($_POST['cust_s_city']),
            strip_tags($_POST['cust_s_zip']),
            $_SESSION['customer']['cust_id']
        ));
    
        $success_message = "Profile Information Updated successfully.";
    
        // Update session variables
        $_SESSION['customer']['cust_s_name'] = $_POST['cust_s_name'];
        $_SESSION['customer']['cust_s_phone'] = $_POST['cust_s_phone'];
        $_SESSION['customer']['cust_s_address'] = $_POST['cust_s_address'];
        $_SESSION['customer']['cust_s_city'] = $_POST['cust_s_city'];
        $_SESSION['customer']['cust_s_zip'] = $_POST['cust_s_zip'];
    }
}
?>


<div class="page">
    <div class="container">
        <div class="row">            
            <div class="col-md-12"> 
                <?php require_once('customer-sidebar.php'); ?>
            </div>
            <div class="col-md-12">
                <div class="user-content">
                    <h3>
                        <?php echo "Update Shipping Info"; ?>
                    </h3>
                    <?php
                    if($error_message != '') {
                        echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                    }
                    if($success_message != '') {
                        echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                    }
                    ?>
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="">Customer Name *</label>
                                <input type="text" class="form-control" name="cust_s_name" value="<?php echo isset($_SESSION['customer']['cust_s_name']) ? $_SESSION['customer']['cust_s_name'] : ''; ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Phone Number *</label>
                                <input type="text" class="form-control" name="cust_s_phone" value="<?php echo isset($_SESSION['customer']['cust_s_phone']) ? $_SESSION['customer']['cust_s_phone'] : ''; ?>">
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="">Address *</label>
                                <textarea name="cust_s_address" class="form-control" cols="30" rows="10" style="height:70px;"><?php echo isset($_SESSION['customer']['cust_s_address']) ? $_SESSION['customer']['cust_s_address'] : ''; ?></textarea>
                            </div>   
                            <div class="col-md-6 form-group">
                                <label for="">City *</label>
                                <input type="text" class="form-control" name="cust_s_city" value="<?php echo isset($_SESSION['customer']['cust_s_city']) ? $_SESSION['customer']['cust_s_city'] : ''; ?>">
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="">Zip Code *</label>
                                <input type="text" class="form-control" name="cust_s_zip" value="<?php echo isset($_SESSION['customer']['cust_s_zip']) ? $_SESSION['customer']['cust_s_zip'] : ''; ?>">
                            </div>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Update" name="form1">
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>
