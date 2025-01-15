<?php require_once('../header.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- ======= Styles ====== -->
    <link rel="stylesheet" href="../../css/products.css?">
    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../css/animations.css">
    <link rel="stylesheet" href="../../css/admin1.css">
    <link rel="stylesheet" href="../../css/admin2.css">
    <title>Manage Products</title>
    <style>
        .dashbord-tables {
            animation: transitionIn-Y-over 0.5s;
        }

        .filter-container {
            animation: transitionIn-Y-bottom 0.5s;
        }

        .sub-table {
            animation: transitionIn-Y-bottom 0.5s;
        }
        

    </style>
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

        <div class="main">
            <div class="topbar">
                <div class="toggle">
                    <ion-icon name="menu-outline"></ion-icon>
                </div>

                <div class="search">
                    <label>
                    <input type="text" id="productSearch" placeholder="Search by name or category">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>

                <div class="user">
                    <img src="assets/imgs/customer01.jpg" alt="">
                </div>
            </div>
            <tr>
                <td colspan="4">
                <div class="abc">
    <table width="100%" class="sub-table" border="0">
        <thead>
            <tr>
                <th class="table-headin">#</th>
                <th class="table-headin">Photo</th>
                <th class="table-headin">Product Name</th>
                <th class="table-headin">Old Price</th>
                <th class="table-headin">(C) Price</th>
                <th class="table-headin">Quantity</th>
                <th class="table-headin">Featured?</th>
                <th class="table-headin">Active?</th>
                <th class="table-headin">Category</th>
                <th class="table-headin">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0;
            $statement = $pdo->prepare("
                SELECT t1.p_id, t1.name, t1.old_price, t1.current_price, t1.quantity, t1.featured_photo,
                t1.is_featured, t1.is_active, t1.ecat_id, 
                IFNULL(t2.ecat_name, 'No Category') AS ecat_name,
                IFNULL(t3.mcat_name, 'No Category') AS mcat_name,
                IFNULL(t4.tcat_name, 'No Category') AS tcat_name
                FROM product t1
                LEFT JOIN end_category t2 ON t1.ecat_id = t2.ecat_id
                LEFT JOIN mid_category t3 ON t2.mcat_id = t3.mcat_id
                LEFT JOIN top_category t4 ON t3.tcat_id = t4.tcat_id
                ORDER BY t1.p_id DESC
            ");
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($result as $row) {
                $i++;
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><img src="../uploads/<?php echo $row['featured_photo']; ?>" alt="<?php echo $row['name']; ?>" style="width:80px;"></td>
                    <td><?php echo $row['name']; ?></td>
                    <td>$<?php echo $row['old_price']; ?></td>
                    <td>$<?php echo $row['current_price']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo ($row['is_featured'] == 1) ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>'; ?></td>
                    <td><?php echo ($row['is_active'] == 1) ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>'; ?></td>
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
<section class="content-header">
            <div class="content-header-right">
                <a href="product-add.php" class="btn btn-primary btn-sm">Add Product</a>
            </div>
        </section>
        </td>
        </tr>
        </tbody>
    </table>
</div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

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
                    <p style="color:red;">Be careful! This product will be deleted from the order table, payment table,
                        size table, color table and rating table also.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-danger btn-ok">Delete</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        console.log("Low stock products:", lowStockProducts);
        var lowStockProducts = <?php echo json_encode($lowStockProducts); ?>;

        // Check if browser supports notifications
        document.addEventListener('DOMContentLoaded', function () {
            if ("Notification" in window) {
                // Request permission if not already granted
                if (Notification.permission !== "granted") {
                    Notification.requestPermission().then(permission => {
                        if (permission !== "granted") {
                            console.log("Notification permission denied.");
                        }
                    });
                }

                // Show notifications for low stock products
                if (lowStockProducts.length > 0 && Notification.permission === "granted") {
                    lowStockProducts.forEach(product => {
                        showLowStockNotification(product.name, product.quantity);
                    });
                }
            }
        });

        // Function to show low stock notification
        function showLowStockNotification(productName, quantity) {
            const options = {
                body: `Only ${quantity} units left for ${productName}. Restock soon!`,
                icon: '../../images/logo.png',
                tag: 'low-stock-notification'
            };
            new Notification(`Low Stock Alert: ${productName}`, options);
        }

        document.getElementById('productSearch').addEventListener('keyup', function () {
            let searchValue = this.value.toLowerCase();
            let rows = document.querySelectorAll('.sub-table tbody tr');

            rows.forEach(row => {
                let productName = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                let category = row.querySelector('td:nth-child(9)').textContent.toLowerCase();

                // Check if search matches either product name or category
                row.style.display = productName.includes(searchValue) || category.includes(searchValue) ? '' : 'none';
            });
        });

        // Sorting Functionality
        document.addEventListener('DOMContentLoaded', function () {
            const oldPriceHeader = document.getElementById('oldPriceHeader');
            const currentPriceHeader = document.getElementById('currentPriceHeader');
            const quantityHeader = document.getElementById('quantityHeader');

            let oldPriceSortOrder = 'asc';
            let currentPriceSortOrder = 'asc';
            let quantitySortOrder = 'asc';

            oldPriceHeader.addEventListener('click', function () {
                sortTableByColumn(4, oldPriceSortOrder);
                oldPriceSortOrder = oldPriceSortOrder === 'asc' ? 'desc' : 'asc';
            });

            currentPriceHeader.addEventListener('click', function () {
                sortTableByColumn(5, currentPriceSortOrder);
                currentPriceSortOrder = currentPriceSortOrder === 'asc' ? 'desc' : 'asc';
            });

            quantityHeader.addEventListener('click', function () {
                sortTableByColumn(6, quantitySortOrder);
                quantitySortOrder = quantitySortOrder === 'asc' ? 'desc' : 'asc';
            });

            function sortTableByColumn(columnIndex, order) {
                let table = document.querySelector('.sub-table tbody');
                let rows = Array.from(table.querySelectorAll('tr'));

                rows.sort((a, b) => {
                    let aText = a.querySelector(`td:nth-child(${columnIndex})`).textContent.replace(/[^\d.-]/g, '');
                    let bText = b.querySelector(`td:nth-child(${columnIndex})`).textContent.replace(/[^\d.-]/g, '');

                    let aValue = parseFloat(aText) || 0;
                    let bValue = parseFloat(bText) || 0;

                    return order === 'asc' ? aValue - bValue : bValue - aValue;
                });

                rows.forEach(row => table.appendChild(row));
            }
        });
    </script>
</body>

</html>
