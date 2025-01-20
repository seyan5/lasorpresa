<?php
require_once('../header.php');
require_once '../auth.php';

if(isset($_POST['id'])) {
    $tcat_id = $_POST['id'];
    $statement = $pdo->prepare("SELECT * FROM mid_category WHERE tcat_id = ? ORDER BY mcat_name ASC");
    $statement->execute([$tcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($result) > 0) {
        echo '<option value="">Select Mid Level Category</option>';
        foreach ($result as $row) {
            echo '<option value="' . $row['mcat_id'] . '">' . $row['mcat_name'] . '</option>';
        }
    } else {
        echo '<option value="">No mid-level categories found</option>';
    }
}
?>