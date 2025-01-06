<?php require_once('../header.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../css/animations.css">  
    <link rel="stylesheet" href="../../css/admin1.css">  
    <link rel="stylesheet" href="../../css/admin2.css">
    <title>Users</title>
</head>

<body>
    <!-- =============== Navigation ================ -->
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
                    <a href="dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Users</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="chatbubble-outline"></ion-icon>
                        </span>
                        <span class="title">Messages</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="settings-outline"></ion-icon>
                        </span>
                        <span class="title">Settings</span>
                    </a>
                </li>

                <li>
                    <a href="../index.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

<section class="content-header">
	<div class="content-header-left">
		<h1>View Products</h1>
	</div>
	<div class="content-header-right">
		<a href="product-add.php" class="btn btn-primary btn-sm">Add Product</a>
	</div>
</section>

<div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <div class="search">
                    <label>
                        <input type="text" placeholder="Search here">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>

                <div class="user">
                    <img src="assets/imgs/customer01.jpg" alt="">
                </div>
            </div>

            <!-- ======================= Cards ================== -->
            <tr>
                <td colspan="4">
                    <div class="abc scroll">
                        <table width="93%" class="sub-table scrolldown" border="0">
                            <thead>
                                <tr>
                                    <th class="table-headin">
                                        Product Name
                                    </th>

                                    <th class="table-headin">
                                        Old Price
                                    </th>
                                    <th class="table-headin">
                                        (C) Price
                                    </th>
                                    <th class="table-headin">
                                        Quantity
                                    </th>
                                    <th class="table-headin">
                                        Featured?
                                    </th>
                                    <th class="table-headin">
                                        Active?
                                    </th>
                                    <th class="table-headin">
                                        Category
                                    </th>

                            </thead>

<!-- <section class="content">
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
						<tbody> -->
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT
														
														t1.p_id,
														t1.name,
														t1.old_price,
														t1.current_price,
														t1.quantity,
														t1.featured_photo,
														t1.is_featured,
														t1.is_active,
														t1.ecat_id,

														t2.ecat_id,
														t2.ecat_name,

														t3.mcat_id,
														t3.mcat_name,

														t4.tcat_id,
														t4.tcat_name

							                           	FROM product t1
							                           	JOIN end_category t2
							                           	ON t1.ecat_id = t2.ecat_id
							                           	JOIN mid_category t3
							                           	ON t2.mcat_id = t3.mcat_id
							                           	JOIN top_category t4
							                           	ON t3.tcat_id = t4.tcat_id
							                           	ORDER BY t1.p_id DESC
							                           	");
							$statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td style="width:82px;"><img src="../uploads/<?php echo $row['featured_photo']; ?>" alt="<?php echo $row['name']; ?>" style="width:80px;"></td>
									<td><?php echo $row['name']; ?></td>
									<td>$<?php echo $row['old_price']; ?></td>
									<td>$<?php echo $row['current_price']; ?></td>
									<td><?php echo $row['quantity']; ?></td>
									<td>
										<?php if($row['is_featured'] == 1) {echo '<span class="badge badge-success" style="background-color:green;">Yes</span>';} else {echo '<span class="badge badge-success" style="background-color:red;">No</span>';} ?>
									</td>
									<td>
										<?php if($row['is_active'] == 1) {echo '<span class="badge badge-success" style="background-color:green;">Yes</span>';} else {echo '<span class="badge badge-danger" style="background-color:red;">No</span>';} ?>
									</td>
									<td><?php echo $row['tcat_name']; ?><br><?php echo $row['mcat_name']; ?><br><?php echo $row['ecat_name']; ?></td>
									<td>										
										<a href="product-edit.php?id=<?php echo $row['p_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
										<a href="#" class="btn btn-danger btn-xs" data-href="product-delete.php?id=<?php echo $row['p_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>  
									</td>
								</tr>
								<?php
							}
							?>							
						</tbody>
					</table>
				</div>
			</div>
		</div>
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
