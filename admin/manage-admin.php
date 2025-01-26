<?php
include('conn.php');


// Check if the user is an admin
if ($_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not admin
    exit;
}

if (isset($_POST['change_user_type'])) {
    $userId = $_POST['user_id'];
    $newUserType = $_POST['user_type'];

    try {
        // Update the user type
        $stmt = $pdo->prepare("UPDATE users SET user_type = :user_type WHERE id = :id");
        $stmt->execute([':user_type' => $newUserType, ':id' => $userId]);

        echo "User type updated successfully!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if (isset($_POST['delete_user'])) {
    $userId = $_POST['user_id'];

    try {
        // Delete the user from the database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);

        echo "User deleted successfully!";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Fetch all users from the database
try {
    $stmt = $pdo->prepare("SELECT id, name, email, user_type FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo "Error fetching users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css?">
    <link rel="stylesheet" href="../css/users.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/admin1.css">  
    <link rel="stylesheet" href="../css/admin2.css">
    <title>Manage Admin</title>

    <style>
        <style>
        :root {
        --blue: #dd91ad;
        --white: #e9e9e9;
        --gray: #f5f5f5;
        --black1: #222;
        --black2: #999;
        }

        .dashbord-tables{
            animation: transitionIn-Y-over 0.5s;
        }
        .filter-container{
            animation: transitionIn-Y-bottom  0.5s;
        }
        .sub-table{
            animation: transitionIn-Y-bottom 0.5s;
        }

        .containers {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .containers table{
            border-radius: 20px
        }

        .scroll {
            overflow-x: auto;
            border-radius: 8px;
        }

        .sub-table {
            width: 100%;
            border-collapse: collapse;
        }


        .sub-table th, .sub-table td {
            text-align: center;
            padding: 12px;
        }

        .usert th  {
            background-color: var(--blue);
            color: #555;
            font-size: 24px; /* Increased font size for header */
            border-bottom: 2px solid #ddd; /* Added underline for header */
            white-space: nowrap; /* Prevents text from wrapping in the header cells */
            text-align: center;
            padding: 12px;
        }

        .sub-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .heading-main12 {
            font-size: 20px;
            color: rgb(255, 255, 255);
        }

        .btn-primary-soft {
            background-color: var(--blue) ;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-primary-soft:hover {
            background-color:rgb(219, 127, 161);
        }

        .non-style-link {
            text-decoration: none;
        }

        .button-icon {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .sub-table th, .sub-table td {
                font-size: 12px;
                padding: 8px;
            }

            .btn-primary-soft {
                font-size: 12px;
                padding: 8px 16px;
            }
        }

        .d-flex {
            display: flex;
        }

        .justify-content-start {
            justify-content: flex-start;
        }

        .mb-3 {
            margin-bottom: 1rem;
        }

        .btn-secondary {
            background-color: var(--blue);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            margin-left: 5rem;
        }

        .btn-secondary:hover {
            background-color: rgb(219, 127, 161);
        }
        
        
    </style>
    </style>
</head>
<body>
    <!-- =============== Navigation ================ -->
    <div class="container">
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <div class="logo-container">
                            <img src="../images/logo.png" alt="Logo" class="logo" />
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
                    <a href="users.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Users</span>
                    </a>
                </li>
                <li>
                    <a href="sales-report.php">
                        <span class="icon">
                            <ion-icon name="cash-outline"></ion-icon>
                        </span>
                        <span class="title">Sales</span>
                    </a>
                </li>
                <li>
                    <a href="product/product.php">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>
                <li>
                    <a href="product/flowers.php">
                        <span class="icon">
                            <ion-icon name="flower-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Flowers</span>
                    </a>
                </li>
                <li>
                    <a href="orders/order.php">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
                    </a>
                </li>
                <li>
                    <a href="customize/customize-order.php">
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
                    <a href="logout.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- ========================= Main ==================== -->
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
            <div class="d-flex justify-content-start mb-3">
            <a href="manage-admin.php" class="btn btn-secondary">Manage Admin</a>
            
        </div>
    <h2>Manage Admin</h2>
    <table border="1" cellspacing="0" cellpadding="5" style="width: 80%; margin: 20px auto; text-align: center;">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($user['user_type'])); ?></td>
                    <td>
                        <!-- Change User Type Button -->
                        <?php if ($user['user_type'] === 'user'): ?>
                            <form action="manage-admin.php" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="user_type" value="admin">
                                <button type="submit" name="change_user_type">Change to Admin</button>
                            </form>
                        <?php elseif ($user['user_type'] === 'admin'): ?>
                            <form action="manage-admin.php" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="user_type" value="user">
                                <button type="submit" name="change_user_type">Change to User</button>
                            </form>
                        <?php endif; ?>

                        <!-- Delete User Button -->
                        <form action="manage-admin.php" method="POST" style="display: inline; margin-left: 10px;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
