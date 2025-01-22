<?php 
require("../header.php");
require_once '../auth.php';
?>


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

<?php
if(isset($_POST['form2'])) {
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

        // Inside your PHP block where you prepare the form data for editing
if (isset($_GET['id'])) {
    $ecat_id = $_GET['id'];
    $statement = $pdo->prepare("SELECT * FROM end_category WHERE ecat_id = ?");
    $statement->execute([$ecat_id]);
    $category = $statement->fetch(PDO::FETCH_ASSOC);
    $ecat_name = $category['ecat_name'];
    $tcat_id = $category['tcat_id']; // Get top-level category id from the record
    $mcat_id = $category['mcat_id']; // Get mid-level category id from the record
} else {
    $ecat_name = '';
    $tcat_id = ''; // Set empty if not editing
    $mcat_id = '';
}

		// updating into the database
		$statement = $pdo->prepare("UPDATE end_category SET ecat_name=?,mcat_id=? WHERE ecat_id=?");
		$statement->execute(array($_POST['ecat_name'],$_POST['mcat_id'],$_REQUEST['id']));

    	$success_message = 'End Level Category is updated successfully.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Sorpresa Admin</title>
    <link rel="stylesheet" href="../../css/settings.css?v.1.0">
    <link rel="stylesheet" href="../../css/products.css">
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    
</head>
<body>
    <div class="container">
        <div class="navigation">
        <ul>
                <li>
                    <a href="#">
                        <div class="logo-container">
                            <img src="../../images/logo.png" alt="Logo" class="logo" />
                        </div>
                        <span class="title"></span>
                    </a>
                </li>
                <li>
                    <a href="../dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="../users.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Users</span>
                    </a>
                </li>
                <li>
                    <a href="../sales-report.php">
                        <span class="icon">
                            <ion-icon name="cash-outline"></ion-icon>
                        </span>
                        <span class="title">Sales</span>
                    </a>
                </li>
                <li>
                    <a href="../product/product.php">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>
                <li>
                    <a href="../product/flowers.php">
                        <span class="icon">
                            <ion-icon name="flower-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Flowers</span>
                    </a>
                </li>
                <li>
                    <a href="../orders/order.php">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
                    </a>
                </li>
                <li>
                    <a href="../customize/customize-order.php">
                        <span class="icon">
                        <ion-icon name="color-wand-outline"></ion-icon>
                        </span>
                        <span class="title"> Customize Orders</span>
                    </a>
                </li>
                <li>
                    <a href="../settings.php">
                        <span class="icon">
                            <ion-icon name="albums-outline"></ion-icon>
                        </span>
                        <span class="title">Categories</span>
                    </a>
                </li>
                <li>
                    <a href="../logout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

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
                                <a href="../settings.php">Color</a>
                                    <a href="../settings/container.php">Container</a>
                                    <a href="../settings/topcategory.php">Top Level Category</a>
                                    <a href="../settings/midcategory.php">Mid Level Category</a>
                                    <a href="../settings/endcategory.php">End Level Category</a>
                                </div>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">

            <div class="row">
            <div class="col-md-12">


            <div class="box box-info">
            <h1>End Level Category</h1>
    <div class="box-body table-responsive table-container">
        <table id="example1" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
			        <th>End Level Category Name</th>
                    <th>Mid Level Category Name</th>
                    <th>Top Level Category Name</th>
			        <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT * 
                                    FROM end_category t1
                                    JOIN mid_category t2
                                    ON t1.mcat_id = t2.mcat_id
                                    JOIN top_category t3
                                    ON t2.tcat_id = t3.tcat_id
                                    ORDER BY t1.ecat_id DESC
                                    ");
            	$statement->execute();
            	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
            	foreach ($result as $row) {
            		$i++;
            		?>
					<tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $row['ecat_name']; ?></td>
                        <td><?php echo $row['mcat_name']; ?></td>
                        <td><?php echo $row['tcat_name']; ?></td>
	                    <td>
                        <a href="#" 
   class="btn btn-primary btn-xs" 
   data-toggle="modal" 
   data-target="#editCategoryModal" 
   data-ecat-id="<?php echo $row['ecat_id']; ?>"
   data-tcat-id="<?php echo $row['tcat_id']; ?>"
   data-mcat-id="<?php echo $row['mcat_id']; ?>"
   data-ecat-name="<?php echo $row['ecat_name']; ?>">
   Edit
</a>
	                        <a href="#" class="btn btn-danger btn-xs" data-href="endcategory-delete.php?id=<?php echo $row['ecat_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
	                    </td>
	                </tr>
            		<?php
            	}
            	?>
            </tbody>
        </table>
    </div>
    <section class="content-header" style="background-color: white !important;">
        <div class="content-header-right">    
            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCategoryModal">Add New</a>
        </div>
    </section>       
</div>
       

        </section>

        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure want to delete this item?</p>
                <p style="color:red;">Be careful! This product will be deleted from the order table, payment table, size table, color table and rating table also.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
            </div>
        </div>
    </div>
</div>


<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryLabel">Add End Level Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Top Level Category Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <select name="tcat_id" id="tcat_id" class="form-control select2 top-cat">
    <option value="">Select Top Level Category</option>
    <?php
    // Assuming $tcat_id is set here
    $statement = $pdo->prepare("SELECT * FROM top_category ORDER BY tcat_name ASC");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $selected = ($row['tcat_id'] == $tcat_id) ? 'selected' : '';
        echo "<option value='{$row['tcat_id']}' $selected>{$row['tcat_name']}</option>";
    }
    ?>
</select>

                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Mid Level Category Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <select name="mcat_id" id="mcat_id" class="form-control select2 mid-cat">
                                        <option value="">Select Mid Level Category</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">End Level Category Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="ecat_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label"></label>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryLabel">Edit End Level Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="tcat_id" class="col-sm-3 control-label">Top Level Category Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <select name="tcat_id" id="tcat_id" class="form-control select2 top-cat">
                                        <option value="">Select Top Level Category</option>
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM top_category ORDER BY tcat_name ASC");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);   
                                        foreach ($result as $row) {
                                            ?>
                                            <option value="<?php echo $row['tcat_id']; ?>" <?php if($row['tcat_id'] == $tcat_id){echo 'selected';} ?>><?php echo $row['tcat_name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mcat_id" class="col-sm-3 control-label">Mid Level Category Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <select name="mcat_id" id="mcat_id" class="form-control select2 mid-cat">
                                        <option value="">Select Mid Level Category</option>
                                        <!-- Mid-level categories will be populated dynamically -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ecat_name" class="col-sm-3 control-label">End Level Category Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" name="ecat_name" value="<?php echo $ecat_name; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success" name="form2">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

        </section>
    </div>

    <script>
    $(document).ready(function() {
        // When the top-level category is changed
        $('#tcat_id').change(function() {
            var tcat_id = $(this).val();
            
            if (tcat_id != '') {
                // Send an AJAX request to fetch mid-level categories
                $.ajax({
                    url: 'fetch-category.php',
                    type: 'POST',
                    data: { tcat_id: tcat_id },
                    success: function(data) {
                        // Populate the Mid Level Category dropdown with the data returned
                        $('#mcat_id').html(data);
                    }
                });
            } else {
                // If no top category is selected, clear the mid-level category options
                $('#mcat_id').html('<option value="">Select Mid Level Category</option>');
            }
        });
    });
</script>

<script>
    // When the modal is shown, set the values
    $('#editCategoryModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal

    // Extract data attributes
    var ecatId = button.data('ecat-id');
    var tcatId = button.data('tcat-id');
    var mcatId = button.data('mcat-id');
    var ecatName = button.data('ecat-name');

    // Set the values in the modal
    var modal = $(this);
    modal.find('input[name="ecat_name"]').val(ecatName);
    modal.find('select[name="tcat_id"]').val(tcatId); // Set selected top category
    modal.find('select[name="mcat_id"]').val(mcatId); // Set selected mid category

    // Fetch Mid Level categories based on selected Top Level category
    $.ajax({
        url: 'fetch-category.php',
        type: 'POST',
        data: { tcat_id: tcatId },
        success: function(data) {
            // Populate the Mid Level Category dropdown with the data returned
            modal.find('select[name="mcat_id"]').html(data);

            // Optionally, you can set the selected Mid Category based on the value
            modal.find('select[name="mcat_id"]').val(mcatId);
        }
    });
});
</script>

</body>


