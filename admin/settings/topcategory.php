<?php require("../header.php");
require_once '../auth.php';
 ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valid = 1;

    if (empty($_POST['tcat_name'])) {
        $valid = 0;
        $error_message .= "Top Category Name cannot be empty<br>";
    } else {
        if (isset($_POST['form_add'])) {
            // Duplicate check for adding
            $statement = $pdo->prepare("SELECT * FROM top_category WHERE tcat_name=?");
            $statement->execute([$_POST['tcat_name']]);
            if ($statement->rowCount() > 0) {
                $valid = 0;
                $error_message .= "Top Category Name already exists<br>";
            }
        } elseif (isset($_POST['form_edit'])) {
            // Duplicate check for editing
            $statement = $pdo->prepare("SELECT * FROM top_category WHERE tcat_id=?");
            $statement->execute([$_POST['tcat_id']]);
            $current = $statement->fetch(PDO::FETCH_ASSOC);
            $current_name = $current['tcat_name'] ?? '';

            $statement = $pdo->prepare("SELECT * FROM top_category WHERE tcat_name=? AND tcat_name!=?");
            $statement->execute([$_POST['tcat_name'], $current_name]);
            if ($statement->rowCount() > 0) {
                $valid = 0;
                $error_message .= "Top Category Name already exists<br>";
            }
        }
    }

    if ($valid) {
        if (isset($_POST['form_add'])) {
            // Add new category
            $statement = $pdo->prepare("INSERT INTO top_category (tcat_name) VALUES (?)");
            $statement->execute([$_POST['tcat_name']]);
            $success_message = "Top Category is added successfully.";
        } elseif (isset($_POST['form_edit'])) {
            // Edit existing category
            $statement = $pdo->prepare("UPDATE top_category SET tcat_name=? WHERE tcat_id=?");
            $statement->execute([$_POST['tcat_name'], $_POST['tcat_id']]);
            $success_message = "Top Category is updated successfully.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Top Category</title>
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
            <h1>Top Level Category</h1>
    <div class="box-body table-responsive table-container">
        <table id="example1" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
			        <th>Top Category Name</th>
                    <!-- <th>Show on Menu?</th> -->
			        <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT * FROM top_category ORDER BY tcat_id DESC");
            	$statement->execute();
            	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
            	foreach ($result as $row) {
            		$i++;
            		?>
					<tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $row['tcat_name']; ?></td>
                        <!-- <td>
                            <?php 
                                if($row['show_on_menu'] == 1) {
                                    echo 'Yes';
                                } else {
                                    echo 'No';
                                }
                            ?>
                        </td> -->
	                    <td>
                        <a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#editModal" data-id="<?php echo $row['tcat_id']; ?>" data-name="<?php echo $row['tcat_name']; ?>">Edit</a>
	                        <a href="#" class="btn btn-danger btn-xs" data-href="topcategory-delete.php?id=<?php echo $row['tcat_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
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
            <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModal">Add New</a>
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

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addModalLabel">Add Top Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="add-tcat-name">Category Name</label>
                                <input type="text" class="form-control" id="add-tcat-name" name="tcat_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="form_add">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Top Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="edit-tcat-id" name="tcat_id">
                            <div class="form-group">
                                <label for="edit-tcat-name">Category Name</label>
                                <input type="text" class="form-control" id="edit-tcat-name" name="tcat_name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="form_edit">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>




        </section>
    </div>

<script>
    $('#editModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var id = button.data('id');
    var name = button.data('name');

    var modal = $(this);
    modal.find('#edit-tcat-id').val(id);
    modal.find('#edit-tcat-name').val(name);
});

</script>
</body>


