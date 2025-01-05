<?php

require_once('../header.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);  // Display errors for debugging

// Check if top category ID is set, then fetch mid-level categories
if (isset($_POST['tcat_id'])) {
    $tcat_id = $_POST['tcat_id'];
    
    // Fetch mid-level categories based on the top category id
    $statement = $pdo->prepare("SELECT * FROM mid_category WHERE tcat_id = ? ORDER BY mcat_name ASC");
    $statement->execute([$tcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($result) > 0) {
        // Return mid-level categories as options
        echo '<option value="">Select Mid Level Category</option>';
        foreach ($result as $row) {
            echo '<option value="' . $row['mcat_id'] . '">' . $row['mcat_name'] . '</option>';
        }
    } else {
        echo '<option value="">No mid-level categories found</option>';
    }
}

// Check if mid category ID is set, then fetch end-level categories
elseif (isset($_POST['mcat_id'])) {
    $mcat_id = $_POST['mcat_id'];
    
    // Fetch end-level categories based on the mid category id
    $statement = $pdo->prepare("SELECT * FROM end_category WHERE mcat_id = ? ORDER BY ecat_name ASC");
    $statement->execute([$mcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($result) > 0) {
        // Return end-level categories as options
        echo '<option value="">Select End Level Category</option>';
        foreach ($result as $row) {
            echo '<option value="' . $row['ecat_id'] . '">' . $row['ecat_name'] . '</option>';
        }
    } else {
        echo '<option value="">No end-level categories found</option>';
    }
}

?>
