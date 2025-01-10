<?php
if(isset($_POST['form1'])) {
    $valid = 1;

    if(empty($_POST['container_name'])) {
        $valid = 0;
        $error_message .= "Container Name cannot be empty<br>";
    } else {
        // Duplicate Container Name checking
        $statement = $pdo->prepare("SELECT * FROM container WHERE container_name=?");
        $statement->execute(array($_POST['container_name']));
        $total = $statement->rowCount();
        if($total) {
            $valid = 0;
            $error_message .= "Container Name already exists<br>";
        }
    }

    if(empty($_POST['container_price']) || !is_numeric($_POST['container_price'])) {
        $valid = 0;
        $error_message .= "Valid price is required<br>";
    }

    if($valid == 1) {
        // Saving data into the container table, including the price
        $statement = $pdo->prepare("INSERT INTO container (container_name, price) VALUES (?, ?)");
        $statement->execute(array($_POST['container_name'], $_POST['container_price']));
        
        $success_message = 'Container is added successfully.';
    }
}
?>


<section class="content-header">
	<div class="content-header-left">
		<h1>Add container</h1>
	</div>
	<div class="content-header-right">
		<a href="container.php" class="btn btn-primary btn-sm">View All</a>
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
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Container Name <span>*</span></label>
							<div class="col-sm-4">
								<input type="text" class="form-control" name="container_name">
							</div>
						</div>
						<div class="form-group">
    <label for="" class="col-sm-2 control-label">Container Price <span>*</span></label>
    <div class="col-sm-4">
        <input type="text" class="form-control" name="container_price" placeholder="Enter Price">
    </div>
</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
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

