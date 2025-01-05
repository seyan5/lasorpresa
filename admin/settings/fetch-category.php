<?php

require_once('../header.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);  // Display errors for debugging

if(isset($_POST['tcat_id'])) {
    $tcat_id = $_POST['tcat_id'];
    
    // Fetch mid-level categories based on the top category id
    $statement = $pdo->prepare("SELECT * FROM mid_category WHERE tcat_id = ? ORDER BY mcat_name ASC");
    $statement->execute([$tcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($result) > 0) {
        foreach ($result as $row) {
            echo '<option value="' . $row['mcat_id'] . '">' . $row['mcat_name'] . '</option>';
        }
    } else {
        echo '<option value="">No mid-level categories found</option>';
    }
}
?>