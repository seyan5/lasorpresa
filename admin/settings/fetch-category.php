<?php
// Include database connection
include('../config.php');

if (isset($_POST['tcat_id'])) {
    $tcat_id = $_POST['tcat_id'];
    $statement = $pdo->prepare("SELECT * FROM mid_category WHERE tcat_id = ? ORDER BY mcat_name ASC");
    $statement->execute([$tcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    $options = '<option value="">Select Mid Level Category</option>';
    foreach ($result as $row) {
        $options .= '<option value="' . $row['mcat_id'] . '">' . $row['mcat_name'] . '</option>';
    }
    echo $options;
}
?>