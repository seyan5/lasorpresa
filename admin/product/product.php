<?php
require_once('../header.php');
?>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Products</h1>
	</div>
	<div class="content-header-right">
		<a href="product-add.php" class="btn btn-primary btn-sm">Add Product</a>
	</div>
</section>

<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-info">
				<div class="box-body table-responsive">
					<table id="example1" class="table table-bordered table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th width="10">#</th>
								<th>Photo</th>
								<th width="160">Product Name</th>
								<th width="60">Old Price</th>
								<th width="60">(C) Price</th>
								<th width="60">Quantity</th>
								<th>Featured?</th>
								<th>Active?</th>
								<th>Category</th>
								<th width="80">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							try {
								$statement = $pdo->prepare("SELECT
            						p.p_id, 
            						p.name AS product_name, 
            						p.old_price, 
            						p.current_price, 
           							p.quantity, 
            						p.product_photo AS photo, 
            						p.is_featured, 
            						p.is_active, 
            						mc.mcat_name AS category_name
        							FROM product p
        							JOIN mid_category mc ON p.ecat_id = mc.mcat_id
    							");
								$statement->execute();
								$result = $statement->fetchAll(PDO::FETCH_ASSOC);

								foreach ($result as $row) {
									echo '<tr>';
									echo '<td>' . $row['p_id'] . '</td>';
									echo '<td><img src="' . $row['photo'] . '" alt="Product Photo"></td>';
									echo '<td>' . $row['product_name'] . '</td>';
									echo '<td>' . $row['old_price'] . '</td>';
									echo '<td>' . $row['current_price'] . '</td>';
									echo '<td>' . $row['quantity'] . '</td>';
									echo '<td>' . ($row['is_featured'] ? 'Yes' : 'No') . '</td>';
									echo '<td>' . ($row['is_active'] ? 'Yes' : 'No') . '</td>';
									echo '<td>' . $row['category_name'] . '</td>';
									echo '<td><a href="edit.php?id=' . $row['p_id'] . '">Edit</a> | <a href="delete.php?id=' . $row['p_id'] . '">Delete</a></td>';
									echo '</tr>';
								}
							} catch (PDOException $e) {
								echo "SQL Error: " . $e->getMessage();
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
	aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
			</div>
			<div class="modal-body">
				<p>Are you sure want to delete this item?</p>
				<p style="color:red;">Be careful! This product will be deleted from the order table, payment table, size
					table, color table and rating table also.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				<a class="btn btn-danger btn-ok">Delete</a>
			</div>
		</div>
	</div>
</div>