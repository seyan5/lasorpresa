<?php require_once('../header.php'); ?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    if(empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a top level category<br>";
    }

    if(empty($_POST['mcat_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a mid level category<br>";
    }

    if(empty($_POST['ecat_name'])) {
        $valid = 0;
        $error_message .= "End level category name can not be empty<br>";
    }

    if($valid == 1) {

		//Saving data into the main table end_category
		$statement = $pdo->prepare("INSERT INTO end_category (ecat_name,mcat_id) VALUES (?,?)");
		$statement->execute(array($_POST['ecat_name'],$_POST['mcat_id']));
	
    	$success_message = 'End Level Category is added successfully.';
    }
}
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>Add End Level Category</h1>
	</div>
	<div class="content-header-right">
		<a href="endcategory.php" class="btn btn-primary btn-sm">View All</a>
	</div>
</section>


<section class="content">

	<div class="row">
		<div class="col-md-12">

			<?php if($error_message): ?>
			<div class="callout callout-danger">
			
			<p>
			<?php echo $error_message; ?>
			</p>
			</div>
			<?php endif; ?>

			<?php if($success_message): ?>
			<div class="callout callout-success">
			
			<p><?php echo $success_message; ?></p>
			</div>
			<?php endif; ?>

			<form class="form-horizontal" action="" method="post">

			<div class="box box-info">
    <div class="box-body">
        <!-- Top Level Category Selection -->
        <div class="form-group">
            <label for="" class="col-sm-3 control-label">Top Level Category Name <span>*</span></label>
            <div class="col-sm-4">
                <select name="tcat_id" id="tcat_id" class="form-control select2 top-cat">
                    <option value="">Select Top Level Category</option>
                    <?php
                    $statement = $pdo->prepare("SELECT * FROM top_category ORDER BY tcat_name ASC");
                    $statement->execute();
                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);    
                    foreach ($result as $row) {
                        ?>
                        <option value="<?php echo $row['tcat_id']; ?>"><?php echo $row['tcat_name']; ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Mid Level Category Selection (Initially empty) -->
        <div class="form-group">
            <label for="" class="col-sm-3 control-label">Mid Level Category Name <span>*</span></label>
            <div class="col-sm-4">
                <select name="mcat_id" id="mcat_id" class="form-control select2 mid-cat">
                    <option value="">Select Mid Level Category</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <label for="" class="col-sm-3 control-label"></label>
            <div class="col-sm-6">
                <button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
            </div>
        </div>
    </div>
</div>

			</form>


		</div>
	</div>

</section>

<script>
$(document).ready(function () {
    // Initialize Select2 Elements
    $(".select2").select2();

    // When a Top Level Category is selected, fetch corresponding Mid Level Categories
    $('#tcat_id').change(function() {
        var tcatId = $(this).val(); // Get selected top category ID

        // Reset the Mid Level Category dropdown
        $('#mcat_id').html('<option value="">Select Mid Level Category</option>');

        // If a Top Level Category is selected, make an AJAX request to fetch Mid Level Categories
        if (tcatId) {
            $.ajax({
                url: 'fetch-category.php',  // URL for PHP script to fetch mid categories
                type: 'POST',
                data: { tcat_id: tcatId },  // Send top category ID
                success: function(response) {
                    $('#mcat_id').html(response); // Populate Mid Level Category dropdown with the response
                    $(".select2").select2(); // Reinitialize Select2 for the new options
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching mid level categories: " + error);
                    alert('Failed to load Mid Level Categories.');
                }
            });
        }
    });
});
</script>

