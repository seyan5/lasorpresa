<?php
require_once('../header.php');
if(isset($_POST['id'])) {
    $mcat_id = $_POST['id'];
    $statement = $pdo->prepare("SELECT * FROM end_category WHERE mcat_id = ? ORDER BY ecat_name ASC");
    $statement->execute([$mcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    if(count($result) > 0) {
        echo '<option value="">Select End Level Category</option>';
        foreach ($result as $row) {
            echo '<option value="' . $row['ecat_id'] . '">' . $row['ecat_name'] . '</option>';
        }
    } else {
        echo '<option value="">No end-level categories found</option>';
    }
}
?>