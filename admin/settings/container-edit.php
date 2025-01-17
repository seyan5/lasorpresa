<?php require_once('../header.php'); ?>

<?php
if(isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';  // Initialize error message

    if(empty($_POST['container_name'])) {
        $valid = 0;
        $error_message .= "Container Name cannot be empty<br>";
    } else {
        // Duplicate container name checking
        $statement = $pdo->prepare("SELECT * FROM container WHERE container_id=?");
        $statement->execute(array($_REQUEST['id']));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach($result as $row) {
            $current_container_name = $row['container_name'];
        }

        $statement = $pdo->prepare("SELECT * FROM container WHERE container_name=? AND container_name!=?");
        $statement->execute(array($_POST['container_name'], $current_container_name));
        $total = $statement->rowCount();                            
        if($total) {
            $valid = 0;
            $error_message .= 'Container name already exists<br>';
        }
    }

    if(empty($_POST['container_price']) || !is_numeric($_POST['container_price'])) {
        $valid = 0;
        $error_message .= "Valid price is required<br>";
    }

    if($valid == 1) {        
        // Handle Image Upload
        $uploaded_image_name = $_FILES['container_image']['name'];
        $uploaded_image_tmp = $_FILES['container_image']['tmp_name'];
        $image_file_name = '';

        if($uploaded_image_name != '') {
            $ext = pathinfo($uploaded_image_name, PATHINFO_EXTENSION);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if(!in_array($ext, $allowed_extensions)) {
                $valid = 0;
                $error_message .= 'You can only upload JPG, JPEG, PNG, or GIF files<br>';
            } else {
                $image_file_name = 'container-' . time() . '.' . $ext;
                move_uploaded_file($uploaded_image_tmp, '../uploads/' . $image_file_name);
            }
        }

        // Update database
        if($valid == 1) {
            if($image_file_name != '') {
                $statement = $pdo->prepare("UPDATE container SET container_name=?, price=?, container_image=? WHERE container_id=?");
                $statement->execute(array($_POST['container_name'], $_POST['container_price'], $image_file_name, $_REQUEST['id']));
            } else {
                $statement = $pdo->prepare("UPDATE container SET container_name=?, price=? WHERE container_id=?");
                $statement->execute(array($_POST['container_name'], $_POST['container_price'], $_REQUEST['id']));
            }

            $success_message = 'Container is updated successfully.';
        }
    }
}
?>

<?php
if(!isset($_REQUEST['id'])) {
    header('location: logout.php');
    exit;
} else {
    // Check if the ID is valid
    $statement = $pdo->prepare("SELECT * FROM container WHERE container_id=?");
    $statement->execute(array($_REQUEST['id']));
    $total = $statement->rowCount();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    if($total == 0) {
        header('location: logout.php');
        exit;
    }
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Edit Container</h1>
    </div>
    <div class="content-header-right">
        <a href="container.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<?php							
foreach ($result as $row) {
    $container_name = $row['container_name'];
    $container_price = $row['price'];
    $container_image = $row['container_image'];  // Fetch existing container image
}
?>

<section class="content">
  <div class="row">
    <div class="col-md-12">
        <?php if(!empty($error_message)): ?>
        <div class="callout callout-danger">
            <p><?php echo $error_message; ?></p>
        </div>
        <?php endif; ?>

        <?php if(!empty($success_message)): ?>
        <div class="callout callout-success">
            <p><?php echo $success_message; ?></p>
        </div>
        <?php endif; ?>

        <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
            <div class="box box-info">
                <div class="box-body">
                    <!-- Container Name -->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Container Name <span>*</span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="container_name" value="<?php echo $container_name; ?>">
                        </div>
                    </div>
                    <!-- Container Price -->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Container Price <span>*</span></label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="container_price" value="<?php echo $container_price; ?>" placeholder="Enter Price">
                        </div>
                    </div>
                    <!-- Container Image -->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Container Image</label>
                        <div class="col-sm-4">
                            <input type="file" name="container_image">
                            <?php if(!empty($container_image)): ?>
                                <img src="../../uploads/<?php echo $container_image; ?>" alt="Container Image" style="width: 80px; height: 80px; margin-top: 10px;">
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label"></label>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-success pull-left" name="form1">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
  </div>
</section>
