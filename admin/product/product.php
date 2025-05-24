<?php require_once('../header.php');
require_once '../auth.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/product.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../css/animations.css">
    <link rel="stylesheet" href="../../css/admin1.css">
    <link rel="stylesheet" href="../../css/admin2.css">
    <title>Admin - Manage Products</title>
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
                    <a href="../wishlist.php">
                        <span class="icon">
                            <ion-icon name="heart-outline"></ion-icon>
                        </span>
                        <span class="title"> Wishlists</span>
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
                <!-- <h1>View Products</h1> -->
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
                        <input type="text" id="productSearch" placeholder="Search by name or category">
                        <ion-icon name="search-outline"></ion-icon>
                    </label>
                </div>

                <div class="user">
                    <img src="assets/imgs/customer01.jpg" alt="">
                </div>
            </div>

            <div class="abc">
                <table width="100%" class="sub-table" border="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>Product Name</th>
                            <th id="oldPriceHeader" style="cursor: pointer;">Old Price <ion-icon
                                    name="swap-vertical-outline"></ion-icon></th>
                            <th id="currentPriceHeader" style="cursor: pointer;">Current Price <ion-icon
                                    name="swap-vertical-outline"></ion-icon></th>
                            <th id="quantityHeader" style="cursor: pointer;">Quantity <ion-icon
                                    name="swap-vertical-outline"></ion-icon></th>
                            <th>Featured?</th>
                            <th>Active?</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        $lowStockProducts = [];
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
                            if ($row['quantity'] <= 10) {
                                $lowStockProducts[] = [
                                    'name' => $row['name'],
                                    'quantity' => $row['quantity']
                                ];
                            }
                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                <td><img src="../uploads/<?php echo $row['featured_photo']; ?>"
                                        alt="<?php echo $row['name']; ?>" style="width:80px;"></td>
                                <td><?php echo $row['name']; ?></td>
                                <td>₱ <?php echo number_format($row['old_price'], 2); ?></td>
                                <td>₱ <?php echo number_format($row['current_price'], 2); ?></td>
                                <td style="color: <?php echo ($row['quantity'] <= 10) ? 'red' : 'black'; ?>;">
                                    <?php echo $row['quantity']; ?>
                                </td>
                                <td><?php echo ($row['is_featured'] == 1) ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>'; ?>
                                </td>
                                <td><?php echo ($row['is_active'] == 1) ? '<span style="color:green;">Yes</span>' : '<span style="color:red;">No</span>'; ?>
                                </td>
                                <td><?php echo $row['tcat_name']; ?><br><?php echo $row['mcat_name']; ?><br><?php echo $row['ecat_name']; ?>
                                </td>
                                <td>
                                    <script>
                                        // Check if the delete success message is set in the session
                                        <?php if (isset($_SESSION['delete_success'])): ?>
                                            Swal.fire({
                                                title: 'Success!',
                                                text: '<?php echo $_SESSION['delete_success']; ?>',
                                                icon: 'success',
                                                confirmButtonText: 'OK'
                                            });
                                            <?php unset($_SESSION['delete_success']); // Unset the session variable after showing the message ?>
                                        <?php endif; ?>
                                    </script>
                                    <a href="product-edit.php?id=<?php echo $row['p_id']; ?>"
                                        class="btn btn-primary btn-xs">Edit</a>
                                    <a href="#" class="btn btn-danger btn-xs" data-id="<?php echo $row['p_id']; ?>"
                                        id="deleteBtn">Delete</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>


    <script>
        console.log("Low stock products:", lowStockProducts);
        var lowStockProducts = <?php echo json_encode($lowStockProducts); ?>;

        // Check if browser supports notifications
        document.addEventListener('DOMContentLoaded', function () {
            if ("Notification" in window) {
                // Request notification permission if not granted
                Notification.requestPermission().then(permission => {
                    if (permission === "granted" && lowStockProducts.length > 0) {
                        let index = 0;

                        // Show notifications at intervals (every 2 seconds)
                        const notificationInterval = setInterval(() => {
                            if (index < lowStockProducts.length) {
                                let product = lowStockProducts[index];
                                showLowStockNotification(product.name, product.quantity);
                                index++;
                            } else {
                                clearInterval(notificationInterval); // Stop interval when all products are notified
                            }
                        }, 2000); // 2000 ms = 2 seconds
                    }
                });
            }
        });

        // Function to show low stock notification
        function showLowStockNotification(productName, quantity) {
            const options = {
                body: `Only ${quantity} units left for ${productName}. Restock soon!`,
                icon: '../../images/logo.png',
                tag: `low-stock-${productName}` // Unique tag for each product
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

        document.querySelectorAll('#deleteBtn').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault(); // Prevent the default action (no page redirect yet)

                const productId = this.getAttribute('data-id');  // Get the product ID from the button's data-id attribute

                // Show SweetAlert confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to remove this product? This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, redirect to product-delete.php with the product ID
                        window.location.href = 'product-delete.php?id=' + productId;
                    }
                });
            });
        });

    </script>
</body>

</html>