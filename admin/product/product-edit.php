<?php
require_once('../header.php');
require_once '../auth.php';

if (isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';

    // Validation
    if (empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "You must select a top-level category.<br>";
    }
    if (empty($_POST['mcat_id'])) {
        $valid = 0;
        $error_message .= "You must select a mid-level category.<br>";
    }
    if (empty($_POST['ecat_id'])) {
        $valid = 0;
        $error_message .= "You must select an end-level category.<br>";
    }
    if (empty($_POST['name'])) {
        $valid = 0;
        $error_message .= "Product name cannot be empty.<br>";
    }
    if (empty($_POST['current_price'])) {
        $valid = 0;
        $error_message .= "Current price cannot be empty.<br>";
    }

    // File Handling
    if (isset($_FILES['product_photo']['name']) && $_FILES['product_photo']['name'] !== '') {
        $path = $_FILES['product_photo']['name'];
        $path_tmp = $_FILES['product_photo']['tmp_name'];

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            $valid = 0;
            $error_message .= "Invalid file format. Only jpg, jpeg, png, and gif are allowed.<br>";
        }
    } else {
        $path = '';
    }

    if ($valid === 1) {
        if ($path === '') {
            // Update without changing the photo
            $statement = $pdo->prepare("UPDATE product SET 
                name=?, 
                old_price=?, 
                current_price=?, 
                quantity=?, 
                description=?, 
                is_featured=?, 
                is_active=?, 
                ecat_id=? 
                WHERE p_id=?");
            $statement->execute([
                $_POST['name'],
                $_POST['old_price'],
                $_POST['current_price'],
                $_POST['quantity'],
                $_POST['description'],
                $_POST['is_featured'],
                $_POST['is_active'],
                $_POST['ecat_id'],
                $_REQUEST['id']
            ]);
        } else {
            // Delete the existing photo if it exists
            if (!empty($_POST['current_photo']) && file_exists('../uploads/' . $_POST['current_photo'])) {
                unlink('../uploads/' . $_POST['current_photo']);
            }

            // Upload the new photo
            $final_name = 'product-featured-' . $_REQUEST['id'] . '.' . $ext;
            move_uploaded_file($path_tmp, '../uploads/' . $final_name);

            // Update with new photo
            $statement = $pdo->prepare("UPDATE product SET 
                name=?, 
                old_price=?, 
                current_price=?, 
                quantity=?, 
                description=?, 
                is_featured=?, 
                is_active=?, 
                ecat_id=?, 
                featured_photo=? 
                WHERE p_id=?");
            $statement->execute([
                $_POST['name'],
                $_POST['old_price'],
                $_POST['current_price'],
                $_POST['quantity'],
                $_POST['description'],
                $_POST['is_featured'],
                $_POST['is_active'],
                $_POST['ecat_id'],
                $final_name,
                $_REQUEST['id']
            ]);
        }

        $success_message = "Product has been updated successfully.";
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Product</h1>
    </div>
    <div class="content-header-right">
        <a href="product.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<?php
$statement = $pdo->prepare("SELECT * FROM product WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $name = $row['name'];
    $old_price = $row['old_price'];
    $current_price = $row['current_price'];
    $quantity = $row['quantity'];
    $featured_photo = $row['featured_photo'];
    $description = $row['description'];
    $feature = $row['feature'];
    $condition = $row['condition'];
    $is_featured = $row['is_featured'];
    $is_active = $row['is_active'];
    $ecat_id = $row['ecat_id'];
}

$statement = $pdo->prepare("SELECT * 
                        FROM end_category t1
                        JOIN mid_category t2
                        ON t1.mcat_id = t2.mcat_id
                        JOIN top_category t3
                        ON t2.tcat_id = t3.tcat_id
                        WHERE t1.ecat_id=?");
$statement->execute(array($ecat_id));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $ecat_name = $row['ecat_name'];
    $mcat_id = $row['mcat_id'];
    $tcat_id = $row['tcat_id'];
}
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">

            <!-- Success and Error Messages -->
            <?php if($error_message): ?>
                <div class="alert alert-danger">
                    <strong>Error!</strong> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if($success_message): ?>
                <div class="alert alert-success">
                    <strong>Success!</strong> <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <!-- Product Form -->
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">

                        <!-- Top Level Category -->
                        <div class="form-group">
                            <label for="tcat_id" class="col-sm-3 control-label">Top Level Category Name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <select name="tcat_id" id="tcat_id" class="form-control select2">
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

                        <!-- Mid Level Category -->
                        <div class="form-group">
                            <label for="mcat_id" class="col-sm-3 control-label">Mid Level Category Name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <select name="mcat_id" id="mcat_id" class="form-control select2">
                                    <option value="">Select Mid Level Category</option>
                                    <?php
                                    $statement = $pdo->prepare("SELECT * FROM mid_category WHERE tcat_id = ? ORDER BY mcat_name ASC");
                                    $statement->execute(array($tcat_id));
                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($result as $row) {
                                        ?>
                                        <option value="<?php echo $row['mcat_id']; ?>" <?php if($row['mcat_id'] == $mcat_id){echo 'selected';} ?>><?php echo $row['mcat_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- End Level Category -->
                        <div class="form-group">
                            <label for="ecat_id" class="col-sm-3 control-label">End Level Category Name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <select name="ecat_id" id="ecat_id" class="form-control select2">
                                    <option value="">Select End Level Category</option>
                                    <?php
                                    $statement = $pdo->prepare("SELECT * FROM end_category WHERE mcat_id = ? ORDER BY ecat_name ASC");
                                    $statement->execute(array($mcat_id));
                                    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                    foreach ($result as $row) {
                                        ?>
                                        <option value="<?php echo $row['ecat_id']; ?>" <?php if($row['ecat_id'] == $ecat_id){echo 'selected';} ?>><?php echo $row['ecat_name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Product Name -->
                        <div class="form-group">
                            <label for="name" class="col-sm-3 control-label">Product Name <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo $name; ?>" required>
                            </div>
                        </div>

                        <!-- Old Price -->
                        <div class="form-group">
                            <label for="old_price" class="col-sm-3 control-label">Old Price</label>
                            <div class="col-sm-4">
                                <input type="text" name="old_price" id="old_price" class="form-control" value="<?php echo $old_price; ?>">
                            </div>
                        </div>

                        <!-- Current Price -->
                        <div class="form-group">
                            <label for="current_price" class="col-sm-3 control-label">Current Price <span class="text-danger">*</span><br></label>
                            <div class="col-sm-4">
                                <input type="text" name="current_price" id="current_price" class="form-control" value="<?php echo $current_price; ?>" required>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="form-group">
                            <label for="quantity" class="col-sm-3 control-label">Quantity <span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="quantity" id="quantity" class="form-control" value="<?php echo $quantity; ?>" required>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-8">
                                <textarea name="description" id="editor1" class="form-control" cols="30" rows="10"><?php echo $description; ?></textarea>
                            </div>
                        </div>

                        <!-- Featured -->
                        <div class="form-group">
                            <label for="is_featured" class="col-sm-3 control-label">Is Featured?</label>
                            <div class="col-sm-8">
                                <select name="is_featured" id="is_featured" class="form-control">
                                    <option value="0" <?php if($is_featured == 0){echo 'selected';} ?>>No</option>
                                    <option value="1" <?php if($is_featured == 1){echo 'selected';} ?>>Yes</option>
                                </select>
                            </div>
                        </div>

                        <!-- Active Status -->
                        <div class="form-group">
                            <label for="is_active" class="col-sm-3 control-label">Is Active?</label>
                            <div class="col-sm-8">
                                <select name="is_active" id="is_active" class="form-control">
                                    <option value="0" <?php if($is_active == 0){echo 'selected';} ?>>No</option>
                                    <option value="1" <?php if($is_active == 1){echo 'selected';} ?>>Yes</option>
                                </select>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="form-group">
                            <label for="product_photo" class="col-sm-3 control-label">Featured Image</label>
                            <div class="col-sm-8">
                                <?php if ($featured_photo != '') { ?>
                                    <img src="../uploads/<?php echo $featured_photo; ?>" height="150" class="img-thumbnail mb-2">
                                <?php } ?>
                                <input type="file" name="product_photo" id="product_photo" class="form-control mb-2">
                                <input type="hidden" name="current_photo" value="<?php echo $featured_photo; ?>">
                            </div>
                        </div>
                    </div>

                    <!-- Form Submit Button -->
                    <div class="box-footer text-center">
                        <button type="submit" name="form1" class="btn btn-success btn-lg">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
