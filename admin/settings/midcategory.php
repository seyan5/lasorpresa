<?php 
require("../header.php");
 require_once '../auth.php';
 ?>

<?php
if(isset($_POST['form2'])) {
	$valid = 1;

    if(empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a top level category<br>";
    }

    if(empty($_POST['mcat_name'])) {
        $valid = 0;
        $error_message .= "Mid Level Category Name can not be empty<br>";
    }

    if ($valid == 1) {    	
        // updating a specific row in the database
        $statement = $pdo->prepare("UPDATE mid_category SET mcat_name=?, tcat_id=? WHERE mcat_id=?");
        $statement->execute(array($_POST['mcat_name'], $_POST['tcat_id'], $_POST['mcat_id'])); 
        $success_message = 'Mid Level Category is updated successfully.';
    }
}
?>

<?php
if(isset($_POST['form1'])) {
	$valid = 1;

    if(empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a top level category<br>";
    }

    if(empty($_POST['mcat_name'])) {
        $valid = 0;
        $error_message .= "Mid Level Category Name can not be empty<br>";
    }

    if($valid == 1) {

		// Saving data into the main table mid_category
		$statement = $pdo->prepare("INSERT INTO mid_category (mcat_name,tcat_id) VALUES (?,?)");
		$statement->execute(array($_POST['mcat_name'],$_POST['tcat_id']));
	
    	$success_message = 'Mid Level Category is added successfully.';
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
            <h1>Mid Level Category</h1>
    <div class="box-body table-responsive table-container">
        <table id="example1" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
			        <th>Mid Level Category Name</th>
                    <th>Top Level Category Name</th>
			        <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT * 
                                    FROM mid_category t1
                                    JOIN top_category t2
                                    ON t1.tcat_id = t2.tcat_id
                                    ORDER BY t1.mcat_id DESC");
            	$statement->execute();
            	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
            	foreach ($result as $row) {
            		$i++;
            		?>
					<tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $row['mcat_name']; ?></td>
                        <td><?php echo $row['tcat_name']; ?></td>
	                    <td>
                            <a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#editCategoryModal" data-id="<?php echo $row['mcat_id']; ?>" data-name="<?php echo $row['mcat_name']; ?>" data-tcat="<?php echo $row['tcat_id']; ?>">Edit</a>
	                        <a href="#" class="btn btn-danger btn-xs" data-href="midcategory-delete.php?id=<?php echo $row['mcat_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
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

<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryLabel">Add New Category</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="tcat_id">Top Level Category</label>
                        <select name="tcat_id" class="form-control">
                            <option value="">Select Top Level Category</option>
                            <?php
                            $statement = $pdo->prepare("SELECT * FROM top_category ORDER BY tcat_name ASC");
                            $statement->execute();
                            $categories = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($categories as $category) {
                                echo "<option value='{$category['tcat_id']}'>{$category['tcat_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mcat_name">Mid Level Category Name</label>
                        <input type="text" name="mcat_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="form1" class="btn btn-success">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryLabel">Edit Category</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="mcat_id" id="edit_mcat_id">
                    <div class="form-group">
                        <label for="tcat_id">Top Level Category</label>
                        <select name="tcat_id" id="edit_tcat_id" class="form-control">
                            <?php
                            foreach ($categories as $category) {
                                echo "<option value='{$category['tcat_id']}'>{$category['tcat_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="mcat_name">Mid Level Category Name</label>
                        <input type="text" name="mcat_name" id="edit_mcat_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="form2" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>












        </section>
    </div>

    <script>
        $(document).on('click', '[data-target="#editCategoryModal"]', function() {
    let id = $(this).data('id');
    let name = $(this).data('name');
    let tcat = $(this).data('tcat');
    
    $('#edit_mcat_id').val(id);
    $('#edit_mcat_name').val(name);
    $('#edit_tcat_id').val(tcat);
});

    </script>
</body>


