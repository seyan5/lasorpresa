<?php
require_once('../header.php');

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
    if (empty($_POST['quantity'])) {
        $valid = 0;
        $error_message .= "Quantity cannot be empty.<br>";
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
                short_description=?, 
                feature=?, 
                `condition`=?, 
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
                $_POST['short_description'],
                $_POST['feature'],
                $_POST['condition'],
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
                short_description=?, 
                feature=?, 
                `condition`=?, 
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
                $_POST['short_description'],
                $_POST['feature'],
                $_POST['condition'],
                $_POST['is_featured'],
                $_POST['is_active'],
                $_POST['ecat_id'],
                $final_name,
                $_REQUEST['id']
            ]);
        }

        // Handle colors
        $statement = $pdo->prepare("DELETE FROM product_color WHERE p_id=?");
        $statement->execute([$_REQUEST['id']]);
        if (!empty($_POST['color'])) {
            foreach ($_POST['color'] as $value) {
                $statement = $pdo->prepare("INSERT INTO product_color (color_id, p_id) VALUES (?, ?)");
                $statement->execute([$value, $_REQUEST['id']]);
            }
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
	$short_description = $row['short_description'];
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

$statement = $pdo->prepare("SELECT * FROM product_color WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
foreach ($result as $row) {
	$color_id[] = $row['color_id'];
}
?>


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

			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">

				<div class="box box-info">
					<div class="box-body">
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Top Level Category Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="tcat_id" class="form-control select2 top-cat">
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
							<label for="" class="col-sm-3 control-label">Mid Level Category Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="mcat_id" class="form-control select2 mid-cat">
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
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">End Level Category Name <span>*</span></label>
							<div class="col-sm-4">
								<select name="ecat_id" class="form-control select2 end-cat">
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
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Product Name <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="name" class="form-control" value="<?php echo $name; ?>">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Old Price<br><span style="font-size:10px;font-weight:normal;">(In USD)</span></label>
							<div class="col-sm-4">
								<input type="text" name="old_price" class="form-control" value="<?php echo $old_price; ?>">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Current Price <span>*</span><br><span style="font-size:10px;font-weight:normal;">(In USD)</span></label>
							<div class="col-sm-4">
								<input type="text" name="current_price" class="form-control" value="<?php echo $current_price; ?>">
							</div>
						</div>	
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Quantity <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" name="quantity" class="form-control" value="<?php echo $quantity; ?>">
							</div>
						</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Select Color</label>
							<div class="col-sm-4">
								<select name="color[]" class="form-control select2" multiple="multiple">
									<?php
									$is_select = '';
									$statement = $pdo->prepare("SELECT * FROM color ORDER BY color_id ASC");
									$statement->execute();
									$result = $statement->fetchAll(PDO::FETCH_ASSOC);			
									foreach ($result as $row) {
										if(isset($color_id)) {
											if(in_array($row['color_id'],$color_id)) {
												$is_select = 'selected';
											} else {
												$is_select = '';
											}
										}
										?>
										<option value="<?php echo $row['color_id']; ?>" <?php echo $is_select; ?>><?php echo $row['color_name']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Existing Featured Photo</label>
							<div class="col-sm-4" style="padding-top:4px;">
								<img src="../assets/uploads/<?php echo $featured_photo; ?>" alt="" style="width:150px;">
								<input type="hidden" name="current_photo" value="<?php echo $featured_photo; ?>">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Change Featured Photo </label>
							<div class="col-sm-4" style="padding-top:4px;">
								<input type="file" name="featured_photo">
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Other Photos</label>
							<div class="col-sm-4" style="padding-top:4px;">
								<table id="ProductTable" style="width:100%;">
			                        <tbody>
			                        	<?php
			                        	$statement = $pdo->prepare("SELECT * FROM product_photo WHERE p_id=?");
			                        	$statement->execute(array($_REQUEST['id']));
			                        	$result = $statement->fetchAll(PDO::FETCH_ASSOC);
			                        	foreach ($result as $row) {
			                        		?>
											<tr>
				                                <td>
				                                    <img src="../uploads/product_photos/<?php echo $row['photo']; ?>" alt="" style="width:150px;margin-bottom:5px;">
				                                </td>
				                                <td style="width:28px;">
				                                	<a onclick="return confirmDelete();" href="product-other-photo-delete.php?id=<?php echo $row['pp_id']; ?>&id1=<?php echo $_REQUEST['id']; ?>" class="btn btn-danger btn-xs">X</a>
				                                </td>
				                            </tr>
			                        		<?php
			                        	}
			                        	?>
			                        </tbody>
			                    </table>
							</div>
							<div class="col-sm-2">
			                    <input type="button" id="btnAddNew" value="Add Item" style="margin-top: 5px;margin-bottom:10px;border:0;color: #fff;font-size: 14px;border-radius:3px;" class="btn btn-warning btn-xs">
			                </div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Description</label>
							<div class="col-sm-8">
								<textarea name="description" class="form-control" cols="30" rows="10" id="editor1"><?php echo $description; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Short Description</label>
							<div class="col-sm-8">
								<textarea name="short_description" class="form-control" cols="30" rows="10" id="editor1"><?php echo $short_description; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Features</label>
							<div class="col-sm-8">
								<textarea name="feature" class="form-control" cols="30" rows="10" id="editor3"><?php echo $feature; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Conditions</label>
							<div class="col-sm-8">
								<textarea name="condition" class="form-control" cols="30" rows="10" id="editor4"><?php echo $condition; ?></textarea>
							</div>
						</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Is Featured?</label>
							<div class="col-sm-8">
								<select name="is_featured" class="form-control" style="width:auto;">
									<option value="0" <?php if($is_featured == '0'){echo 'selected';} ?>>No</option>
									<option value="1" <?php if($is_featured == '1'){echo 'selected';} ?>>Yes</option>
								</select> 
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Is Active?</label>
							<div class="col-sm-8">
								<select name="is_active" class="form-control" style="width:auto;">
									<option value="0" <?php if($is_active == '0'){echo 'selected';} ?>>No</option>
									<option value="1" <?php if($is_active == '1'){echo 'selected';} ?>>Yes</option>
								</select> 
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label"></label>
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