<?php
require_once("../header.php");
require_once '../auth.php';

// Initialize messages
$error_message = '';
$success_message = '';

// Add Container Logic
if(isset($_POST['addContainer'])) {
    $valid = 1;

    // Validate Container Name
    if(empty($_POST['container_name'])) {
        $valid = 0;
        $error_message .= "Container Name cannot be empty<br>";
    } else {
        // Check for duplicate container name
        $statement = $pdo->prepare("SELECT * FROM container WHERE container_name=?");
        $statement->execute(array($_POST['container_name']));
        if($statement->rowCount() > 0) {
            $valid = 0;
            $error_message .= "Container Name already exists<br>";
        }
    }

    // Validate Container Price
    if(empty($_POST['container_price']) || !is_numeric($_POST['container_price'])) {
        $valid = 0;
        $error_message .= "Valid price is required<br>";
    }

    // Validate and handle Container Image
    if(empty($_FILES['container_image']['name'])) {
        $valid = 0;
        $error_message .= "Container Image cannot be empty<br>";
    } else {
        $path = $_FILES['container_image']['name'];
        $path_tmp = $_FILES['container_image']['tmp_name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        if(!in_array($ext, $allowed_ext)) {
            $valid = 0;
            $error_message .= "You must upload a valid image file (jpg, jpeg, png, gif)<br>";
        } else {
            $final_name = 'container_' . time() . '.' . $ext;
            move_uploaded_file($path_tmp, '../uploads/' . $final_name);
        }
    }

    if($valid == 1) {
        $statement = $pdo->prepare("INSERT INTO container (container_name, price, container_image) VALUES (?, ?, ?)");
        $statement->execute(array($_POST['container_name'], $_POST['container_price'], $final_name));
        $success_message = 'Container is added successfully.';
    }
}

// Edit Container Logic
if(isset($_POST['editContainer'])) {
    $valid = 1;

    // Validate Container Name
    if(empty($_POST['container_name'])) {
        $valid = 0;
        $error_message .= "Container Name cannot be empty<br>";
    } else {
        // Check for duplicate container name
        $statement = $pdo->prepare("SELECT * FROM container WHERE container_name=? AND container_id!=?");
        $statement->execute(array($_POST['container_name'], $_POST['container_id']));
        if($statement->rowCount() > 0) {
            $valid = 0;
            $error_message .= "Container Name already exists<br>";
        }
    }

    // Validate Container Price
    if(empty($_POST['container_price']) || !is_numeric($_POST['container_price'])) {
        $valid = 0;
        $error_message .= "Valid price is required<br>";
    }

    // Validate and handle Container Image
    $image_file_name = '';
    if(!empty($_FILES['container_image']['name'])) {
        $path = $_FILES['container_image']['name'];
        $path_tmp = $_FILES['container_image']['tmp_name'];
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        if(!in_array($ext, $allowed_ext)) {
            $valid = 0;
            $error_message .= "You must upload a valid image file (jpg, jpeg, png, gif)<br>";
        } else {
            $image_file_name = 'container_' . time() . '.' . $ext;
            move_uploaded_file($path_tmp, '../uploads/' . $image_file_name);
        }
    }

    if($valid == 1) {
        if($image_file_name != '') {
            $statement = $pdo->prepare("UPDATE container SET container_name=?, price=?, container_image=? WHERE container_id=?");
            $statement->execute(array($_POST['container_name'], $_POST['container_price'], $image_file_name, $_POST['container_id']));
        } else {
            $statement = $pdo->prepare("UPDATE container SET container_name=?, price=? WHERE container_id=?");
            $statement->execute(array($_POST['container_name'], $_POST['container_price'], $_POST['container_id']));
        }
        $success_message = 'Container is updated successfully.';
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
            <h1>Container</h1>
    <div class="box-body table-responsive table-container">

        <table id="example1" class="table table-bordered table-hover table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Container Name</th>
            <th>Container Price</th>
            <th>Container Image</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        $statement = $pdo->prepare("SELECT * FROM container ORDER BY container_id ASC");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $i++;
            ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['container_name']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td>
                    <?php if (!empty($row['container_image'])): ?>
                        <img src="../uploads/<?php echo $row['container_image']; ?>" alt="Container Image" style="width: 80px; height: 80px;">
                    <?php else: ?>
                        <p>No Image</p>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="#" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#editContainerModal" data-id="<?php echo $row['container_id']; ?>" data-name="<?php echo $row['container_name']; ?>">Edit</a>

                    <a href="#" class="btn btn-danger btn-xs" data-href="container-delete.php?id=<?php echo $row['container_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
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
        <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addContainerModal">Add New</a>
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

<!-- Add/Edit Container Modals -->
<div class="modal fade" id="addContainerModal" tabindex="-1" aria-labelledby="addContainerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addContainerLabel">Add New Container</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="container_name" class="form-label">Container Name</label>
                        <input type="text" class="form-control" name="container_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="container_price" class="form-label">Container Price</label>
                        <input type="number" class="form-control" name="container_price" required>
                    </div>
                    <div class="mb-3">
                        <label for="container_image" class="form-label">Container Image</label>
                        <input type="file" class="form-control" name="container_image" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="addContainer">Add Container</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editContainerModal" tabindex="-1" aria-labelledby="editContainerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="editContainerLabel">Edit Container</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="container_id" id="editContainerId">
                    <div class="mb-3">
                        <label for="container_name" class="form-label">Container Name</label>
                        <input type="text" class="form-control" name="container_name" id="editContainerName" required>
                    </div>
                    <div class="mb-3">
                        <label for="container_price" class="form-label">Container Price</label>
                        <input type="number" class="form-control" name="container_price" id="editContainerPrice" required>
                    </div>
                    <div class="mb-3">
                        <label for="container_image" class="form-label">Container Image</label>
                        <input type="file" class="form-control" name="container_image" id="editContainerImage">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="editContainer">Update Container</button>
                </div>
            </form>
        </div>
    </div>
</div>



        </section>
    </div>

    <script>
    $('#editContainerModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Triggered by 'Edit' button
        var containerId = button.data('id');
        var containerName = button.data('name');
        var containerPrice = button.data('price');
        var containerImage = button.data('image');

        var modal = $(this);
        modal.find('#editContainerId').val(containerId);
        modal.find('#editContainerName').val(containerName);
        modal.find('#editContainerPrice').val(containerPrice);

        if (containerImage) {
            modal.find('#editContainerImage').val(containerImage);
        }
    });
    
</script>


</body>


