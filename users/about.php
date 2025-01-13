<?php
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- font -->
    <title>Flowers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- css -->
    <link rel="stylesheet" href="../css/navhead.css">
</head>

<body>
    <!-- header -->

    <header>

        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>

        <!-- <a href="#" class="logo">Flower<span>.</span></a> -->
        <img src="../images/logo.png" alt="" class="logos" href="">
        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="#about">About</a>
            <div class="prod-dropdown">
                <a href="" onclick="toggleDropdown()">Products</a>
                <div class="prod-menu" id="prodDropdown">
                    <a href="products.php">Flowers</a>
                    <a href="occasion.php">Occasion</a>
                    <a href="addons.php">Addons</a>
                </div>
            </div>
            <a href="#review">Review</a>
            <a href="#contacts">Contacts</a>
            <a href="customization.php">Customize</a>

        </nav>

        <div class="icons">
            <a href="shopcart.php" class="fas fa-shopping-cart"></a>
            <div class="user-dropdown">
                <a href="#" class="fas fa-user" onclick="toggleDropdown()"></a>
                <div class="dropdown-menu" id="userDropdown">
                    <?php if (isset($_SESSION['customer'])): ?>
                        <p>Welcome, <?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?></p>
                        <hr>
                        <a href="profile.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    <body>
    <!-- header -->
    <header>
        <input type="checkbox" name="" id="toggler">
        <label for="toggler" class="fas fa-bars"></label>
        <img src="../images/logo.png" alt="" class="logos" href="">

        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="#about">About</a>
            <div class="prod-dropdown">
                <a href="" onclick="toggleDropdown()">Products</a>
                <div class="prod-menu" id="prodDropdown">
                    <a href="products.php">Flowers</a>
                    <a href="occasion.php">Occasion</a>
                    <a href="addons.php">Addons</a>
                </div>
            </div>
            <a href="#review">Review</a>
            <a href="#contacts">Contacts</a>
            <a href="customization.php">Customize</a>
        </nav>

        <div class="icons">
            <a href="shopcart.php" class="fas fa-shopping-cart"></a>
            <div class="user-dropdown">
                <a href="#" class="fas fa-user" onclick="toggleDropdown()"></a>
                <div class="dropdown-menu" id="userDropdown">
                    <?php if (isset($_SESSION['customer'])): ?>
                        <p>Welcome, <?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?></p>
                        <hr>
                        <a href="profile.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- About Section -->
    <section class="about-us" id="about" style="padding: 8.7rem; text-align: center;">
        <h2 style="font-size: 5rem; margin-bottom: 2rem;">About Us</h2>
        <p style = "font-size: 1.5rem;" >
            Welcome to <strong>La Sorpresa by J & B</strong>, your trusted flower shop where elegance meets creativity.
            Founded by two passionate individuals, J & B, our shop was born out of a shared love for flowers and a
            commitment to spreading joy through nature’s most beautiful creations.
            </p>
            <p style = "font-size: 1.5rem;" >
            At La Sorpresa by J & B, we specialize in crafting bespoke floral arrangements for every occasion—whether
            it’s a heartfelt surprise, a romantic gesture, or a grand celebration. Each bouquet and design is
            thoughtfully curated to reflect the emotions and stories behind them, making every arrangement as unique as
            the person receiving it.
            </p>
            <p style = "font-size: 1.5rem;" >
            Our mission is simple: to create meaningful moments through the art of floristry. We source only the
            freshest and most vibrant blooms, ensuring quality and beauty in every petal. From timeless roses to exotic
            blooms, we blend creativity and passion to bring your floral visions to life.
            </p>
            <p style = "font-size: 1.5rem;" >
            More than just a flower shop, <strong>La Sorpresa by J & B</strong> is a celebration of love, life, and
            surprises. Let us help you make every occasion unforgettable with the perfect arrangement crafted just for
            you.
            <strong>Surprise them beautifully, with La Sorpresa by J & B.</strong>
            </p>
        </p>
    </section>
</body>
