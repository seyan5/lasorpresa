<?php
// Database connection
require_once('../header.php');
include('../auth.php');



$stmt = $pdo->prepare("SELECT * FROM flowers");
$stmt->execute();
$flowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
$i = 1;  // Initialize $i for product numbering
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <link rel="stylesheet" href="../../css/animations.css">  
    <link rel="stylesheet" href="../../css/admin1.css">  
    <link rel="stylesheet" href="../../css/admin2.css">
    <title>Admin - Manage Flowers</title>
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
                    <a href="product.php">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>
                <li>
                    <a href="flowers.php">
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

        <section class="content-header">
            <div class="content-header-left">
                <h1>View Products</h1>
            </div>
            <div class="content-header-right">
                <!-- Button to trigger Add Product Modal -->
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addFlowerModal">Add Product</button>
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

            <!-- Product Table -->
            <div class="abc scroll">
                <table width="93%" class="sub-table scrolldown" border="0">
                    <thead>
                        <tr>
                            <th class="table-headin">#</th>
                            <th class="table-headin">Photo</th>
                            <th class="table-headin">Product Name</th>
                            <th class="table-headin">(C) Price</th>
                            <!-- <th class="table-headin">Quantity</th> -->
                            <th class="table-headin">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flowers as $flower): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td style="width:82px;">
                                    <img src="<?php echo htmlspecialchars($flower['image']); ?>" alt="<?php echo htmlspecialchars($flower['name']); ?>" width="50">
                                </td>
                                <td><?php echo htmlspecialchars($flower['name']); ?></td>
                                <td>$<?php echo number_format($flower['price'], 2); ?></td>
                                <!-- <td><?php echo htmlspecialchars($flower['quantity']); ?></td> -->
                                <td>										
                                    <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#editFlowerModal" 
                                        data-id="<?php echo $flower['id']; ?>" 
                                        data-name="<?php echo htmlspecialchars($flower['name']); ?>" 
                                        data-quantity="<?php echo $flower['quantity']; ?>" 
                                        data-price="<?php echo $flower['price']; ?>">
                                        Edit
                                    </button>
                                    <a href="#" class="btn btn-danger btn-xs" data-href="flower-delete.php?id=<?php echo $flower['id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>  
                                </td>
                            </tr>
                        <?php endforeach; ?>					
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Product Modal -->
        <div class="modal fade" id="addFlowerModal" tabindex="-1" role="dialog" aria-labelledby="addFlowerModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFlowerModalLabel">Add New Flower</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="flower-add.php" method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name">Flower Name:</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label for="price">Price:</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label for="image">Flower Image:</label>
                                <input type="file" name="image" id="image" class="form-control">
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Flower</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Product Modal -->
<div class="modal fade" id="editFlowerModal" tabindex="-1" role="dialog" aria-labelledby="editFlowerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFlowerModalLabel">Edit Flower</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Update form for editing flowers -->
                <form action="flower-update.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit-id">
                    
                    <!-- Flower Name -->
                    <div class="form-group">
                        <label for="edit-name">Flower Name:</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group">
                        <label for="edit-quantity">Quantity:</label>
                        <input type="number" name="quantity" id="edit-quantity" class="form-control" step="0.01" required>
                    </div>

                    <!-- Price -->
                    <div class="form-group">
                        <label for="edit-price">Price:</label>
                        <input type="number" name="price" id="edit-price" class="form-control" step="0.01" required>
                    </div>

                    <!-- Flower Image -->
                    <div class="form-group">
                        <label for="edit-image">Flower Image:</label>
                        <input type="file" name="image" id="edit-image" class="form-control">
                        <img id="current-image" src="#" alt="Flower Image" style="max-width: 100px; margin-top: 10px;">
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this item?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-danger btn-ok" href="#">Delete</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle dynamic delete modal
        $('#confirm-delete').on('show.bs.modal', function(e) {
            var href = $(e.relatedTarget).data('href');
            $(this).find('.btn-ok').attr('href', href);
        });

        // Populate Edit Modal with product data
        $('#editFlowerModal').on('show.bs.modal', function(e) {
            var button = $(e.relatedTarget);
            $('#edit-id').val(button.data('id'));
            $('#edit-name').val(button.data('name'));
            $('#edit-quantity').val(button.data('quantity'));
            $('#edit-price').val(button.data('price'));
        });
    </script>
</body>

</html>
