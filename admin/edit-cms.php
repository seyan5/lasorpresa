<?php 
ob_start();
include_once('conn.php');
include('auth.php');

// Ensure $pdo is a valid PDO instance
if (!isset($pdo)) {
    die("<script>Swal.fire({ icon: 'error', title: 'Database connection error.' });</script>");
}

// Fetch current content
$stmt = $pdo->query("SELECT * FROM content WHERE id=1");
$content = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['about' => '', 'image' => '', 'logo' => '', 'video' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $about = isset($_POST['about']) ? $_POST['about'] : $content['about'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = '../ivd/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    } else {
        $image = $content['image'];
    }

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $logo = '../ivd/' . basename($_FILES['logo']['name']);
        move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
    } else {
        $logo = $content['logo'];
    }

    if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
        $video = '../ivd/' . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], $video);
    } else {
        $video = $content['video'];
    }

    $stmt = $pdo->prepare("UPDATE content SET about=:about, image=:image, logo=:logo, video=:video WHERE id=1");
    $stmt->execute(['about' => $about, 'image' => $image, 'logo' => $logo, 'video' => $video]);
    
    echo "<script>Swal.fire({ icon: 'success', title: 'Content updated successfully!' });</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Sorpresa Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <a href="wishlist.php">
                        <span class="icon">
                        <ion-icon name="heart-outline"></ion-icon>
                        </span>
                        <span class="title"> Wishlists</span>
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

        <div class="main">
            <h2>Update Content</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="about">About:</label>
                <textarea name="about" id="about" required><?php echo htmlspecialchars($content['about']); ?></textarea>
                
                <label for="image">Upload Image:</label>
                <input type="file" name="image" id="image">
                <img src="<?php echo $content['image']; ?>" alt="Current Image" width="100">
                
                <label for="logo">Upload Logo:</label>
                <input type="file" name="logo" id="logo">
                <img src="<?php echo $content['logo']; ?>" alt="Current Logo" width="100">
                
                <label for="video">Upload Video:</label>
                <input type="file" name="video" id="video">
                <video width="200" controls><source src="<?php echo $content['video']; ?>" type="video/mp4"></video>
                
                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</body>
</html>