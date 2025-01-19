<?php
session_start(); // Start the session
require_once('conn.php'); // Include your database connection
?>


<?php
// Check if the session is active
if (!isset($_SESSION['customer'])) {
    // Redirect to logout or login page if session is missing
    header('Location: ' . BASE_URL . 'logout.php');
    exit;
} else {
    // Verify the user is still active
    $statement = $pdo->prepare("SELECT * FROM customer WHERE cust_id = ? AND cust_status = ?");
    $statement->execute([$_SESSION['customer']['cust_id'], 0]);
    if ($statement->rowCount() > 0) {
        // If user is inactive, log them out
        header('Location: ' . BASE_URL . 'logout.php');
        exit;
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
                    <h3 class="text-center">
                        <?php echo "Dashboard"; ?>
                    </h3>
                </div>                
            </div>
        </div>
    </div>
</div>

