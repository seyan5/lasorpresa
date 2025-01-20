<?php 
require("../header.php");
require_once '../auth.php';

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
                    <a href="settings.php">
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
            <h1>End Level Category</h1>
    <div class="box-body table-responsive table-container">
        <table id="example1" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
			        <th>End Level Category Name</th>
                    <th>Mid Level Category Name</th>
                    <th>Top Level Category Name</th>
			        <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            	$i=0;
            	$statement = $pdo->prepare("SELECT * 
                                    FROM end_category t1
                                    JOIN mid_category t2
                                    ON t1.mcat_id = t2.mcat_id
                                    JOIN top_category t3
                                    ON t2.tcat_id = t3.tcat_id
                                    ORDER BY t1.ecat_id DESC
                                    ");
            	$statement->execute();
            	$result = $statement->fetchAll(PDO::FETCH_ASSOC);							
            	foreach ($result as $row) {
            		$i++;
            		?>
					<tr>
	                    <td><?php echo $i; ?></td>
	                    <td><?php echo $row['ecat_name']; ?></td>
                        <td><?php echo $row['mcat_name']; ?></td>
                        <td><?php echo $row['tcat_name']; ?></td>
	                    <td>
	                        <a href="endcategory-edit.php?id=<?php echo $row['ecat_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
	                        <a href="#" class="btn btn-danger btn-xs" data-href="endcategory-delete.php?id=<?php echo $row['ecat_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
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
            <a href="endcategory-add.php" class="btn btn-primary btn-sm">Add New</a>
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

        </section>
    </div>
</body>


