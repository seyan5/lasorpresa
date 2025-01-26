<?php
require_once('header.php');
require_once 'auth.php';

if(isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';
    $success_message = '';

    // Validation: check if the color name is empty
    if(empty($_POST['color_name'])) {
        $valid = 0;
        $error_message .= "Color Name cannot be empty<br>";
    } else {
        // Duplicate Color Name check
        $statement = $pdo->prepare("SELECT * FROM color WHERE color_name=?");
        $statement->execute(array($_POST['color_name']));
        $total = $statement->rowCount();
        if($total) {
            $valid = 0;
            $error_message .= "Color Name already exists<br>";
        }
    }

    if($valid == 1) {
        // Saving data into the color table
        $statement = $pdo->prepare("INSERT INTO color (color_name) VALUES (?)");
        $statement->execute(array($_POST['color_name']));
        $success_message = 'Color is added successfully.';
    }

    // Return response as JSON (AJAX)
    if(isset($_POST['form1']) && !empty($_POST['form1'])) {
        if($valid == 1) {
            echo json_encode(['success' => $success_message]);
        } else {
            echo json_encode(['error' => $error_message]);
        }
        exit();
    }
}

if(isset($_POST['form2'])) {
    $valid = 1;
    $error_message = '';
    $success_message = '';

    // Validation: check if the color name is empty
    if(empty($_POST['color_name'])) {
        $valid = 0;
        $error_message .= "Color Name cannot be empty<br>";
    } else {
        // Duplicate Color Name check
        $statement = $pdo->prepare("SELECT * FROM color WHERE color_name=? AND color_id != ?");
        $statement->execute([$_POST['color_name'], $_POST['color_id']]);
        $total = $statement->rowCount();
        if ($total) {
            $valid = 0;
            $error_message .= "Color Name already exists<br>";
        }
    }

    if ($valid == 1) {
        $statement = $pdo->prepare("UPDATE color SET color_name=? WHERE color_id=?");
        $statement->execute([$_POST['color_name'], $_POST['color_id']]);
        $success_message = 'Color updated successfully.';
    }

    // Return response as JSON (AJAX)
    if(isset($_POST['form2']) && !empty($_POST['form2'])) {
        if($valid == 1) {
            echo json_encode(['success' => $success_message]);
        } else {
            echo json_encode(['error' => $error_message]);
        }
        exit();
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
    <link rel="stylesheet" href="../css/settings.css?v.1.1">
    <link rel="stylesheet" href="../css/products.css">
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
                            <img src="../images/logo.png" alt="Logo" class="logo" />
                        </div>
                        <span class="title"></span>
                    </a>
                </li>
                <li>
                    <a href="dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="users.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Users</span>
                    </a>
                </li>
                <li>
                    <a href="sales-report.php">
                        <span class="icon">
                            <ion-icon name="cash-outline"></ion-icon>
                        </span>
                        <span class="title">Sales</span>
                    </a>
                </li>
                <li>
                    <a href="product/product.php">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>
                <li>
                    <a href="product/flowers.php">
                        <span class="icon">
                            <ion-icon name="flower-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Flowers</span>
                    </a>
                </li>
                <li>
                    <a href="orders/order.php">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
                    </a>
                </li>
                <li>
                    <a href="customize/customize-order.php">
                        <span class="icon">
                        <ion-icon name="color-wand-outline"></ion-icon>
                        </span>
                        <span class="title"> Customize Orders</span>
                    </a>
                </li>
                <li>
                    <a href="settings.php">
                        <span class="icon">
                            <ion-icon name="albums-outline"></ion-icon>
                        </span>
                        <span class="title">Categories</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
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
                                    <a href="settings.php">Color</a>
                                    <a href="settings/container.php">Container</a>
                                    <a href="settings/topcategory.php">Top Level Category</a>
                                    <a href="settings/midcategory.php">Mid Level Category</a>
                                    <a href="settings/endcategory.php">End Level Category</a>
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
            <h1>Colors</h1>
            <div class="box-body table-responsive table-container">
                <table id="example1" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Color Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $statement = $pdo->prepare("SELECT * FROM color ORDER BY color_id ASC");
                        $statement->execute();
                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);							
                        foreach ($result as $row) {
                            $i++;
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><?php echo $row['color_name']; ?></td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#editColorModal" data-id="<?php echo $row['color_id']; ?>" data-name="<?php echo $row['color_name']; ?>">Edit</a>
                                    <a href="#" class="btn btn-danger btn-xs" data-href="settings/color-delete.php?id=<?php echo $row['color_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
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
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addColorModal">Add New</button>
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

<!-- Add New Color Modal -->
<div class="modal fade" id="addColorModal" tabindex="-1" role="dialog" aria-labelledby="addColorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addColorModalLabel">Add New Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Error or Success Messages -->
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php endif; ?>

                <!-- Add New Color Form -->
                <form id="addColorForm" class="form-horizontal" action="" method="post">
                    <div class="form-group">
                        <label for="color_name">Color Name <span>*</span></label>
                        <input type="text" class="form-control" name="color_name" id="color_name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="addColorForm" name="form1">Add Color</button>
            </div>
        </div>
    </div>
</div>


<!-- Edit Color Modal -->
<div class="modal fade" id="editColorModal" tabindex="-1" role="dialog" aria-labelledby="editColorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editColorModalLabel">Edit Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Error or Success Messages -->
                <div id="edit-error-message" class="alert alert-danger" style="display:none;"></div>
                <div id="edit-success-message" class="alert alert-success" style="display:none;"></div>

                <!-- Edit Color Form -->
                <form id="editColorForm" class="form-horizontal" action="" method="post">
                    <input type="hidden" name="color_id" id="editColorId">
                    <div class="form-group">
                        <label for="edit_color_name">Color Name <span>*</span></label>
                        <input type="text" class="form-control" name="color_name" id="edit_color_name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="editColorForm" name="form2">Save Changes</button>
            </div>
        </div>
    </div>
</div>


        </section>
    </div>

</body>



<script>
    // JavaScript for Edit Color Modal
$('#editColorModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Button that triggered the modal
    var colorId = button.data('id'); // Extract info from data-* attributes
    var colorName = button.data('name');

    // Populate the form with the color data
    var modal = $(this);
    modal.find('#editColorId').val(colorId);
    modal.find('#edit_color_name').val(colorName);
});



</script>



