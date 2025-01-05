<?php
require_once('../header.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['tcat_id'])) {
    $tcat_id = $_POST['tcat_id'];

    try {

        // Prepare and execute query
        $statement = $pdo->prepare("SELECT * FROM mid_category WHERE tcat_id = ? ORDER BY mcat_name ASC");
        $statement->execute([$tcat_id]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Check if results are returned
        if ($result) {
            $options = '<option value="">Select Mid Level Category</option>';
            foreach ($result as $row) {
                $options .= '<option value="' . $row['mcat_id'] . '">' . $row['mcat_name'] . '</option>';
            }
            echo $options;
        } else {
            echo '<option value="">No mid-level categories available</option>';
        }
    } catch (PDOException $e) {
        echo '<option value="">Error: ' . $e->getMessage() . '</option>';
    }
} else {
    echo '<option value="">Invalid request</option>';
}
?>