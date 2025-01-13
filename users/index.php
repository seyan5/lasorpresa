<!-- This is main configuration File -->
<?php
ob_start();
session_start();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <!-- css -->
     <link rel="stylesheet" href="../css/footer.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/main.css?v=1.2">
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
                        <a href="customer-profile-update.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </header>

    <!-- lheader -->

    <section class="home" id="home">
        <div class="content">
            <h3>Say it</h3>
            <h3>With</h3>
            <h3>Flowers</h3>
            <div class="container">
                <div class="item">
                    <h1>30+</h1>
                    <p>Flowers</p>
                </div>
                <div class="line"></div>
                <div class="item">
                    <h1>100+</h1>
                    <p>Customers</p>
                </div>
            </div>
            <!--<a href="#" class="btn">Shop now!</a>-->
            <div class="circle"></div>
        </div>
        <div class="search-container">
            <input type="text" placeholder="What are you looking for?" class="search-input">
            <button class="search-button"><i class="fas fa-search"></i></button>
        </div>
    </section>



    <!-- prod sec -->
    <section class="products" id="products">

        <?php
        // Fetch products for mid-category ID = 3 and is_featured = 1
// Fetch the latest 3 products for mid-category ID = 3 and is_featured = 1
        $statement = $pdo->prepare("
    SELECT p.p_id, p.name, p.featured_photo, p.current_price, p.old_price 
    FROM product p
    JOIN end_category ec ON p.ecat_id = ec.ecat_id
    WHERE ec.mcat_id = 3 AND p.is_active = 1 AND p.is_featured = 1
    ORDER BY p.p_id DESC  -- Order by product ID in descending order to show the latest products first
    LIMIT 3  -- Limit the results to 3 products
");
        $statement->execute();
        $products = $statement->fetchAll(PDO::FETCH_ASSOC);

        ?>

        <h1 class="heading">Latest <span>Flowers</span></h1>
        <div class="box-container">
            <div class="shop">
                <h1>Best Selling</h1>
                <h1>Flowers</h1>
                <h3>Cheap and</h3>
                <h3>Affordable Flowers</h3>
                <a href="products.php" class="btn">See more &gt;</a>
            </div>

            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    <div class="box">
                        <span class="discount">-10%</span>
                        <div class="image">
                            <img src="../admin/uploads/<?php echo htmlspecialchars($product['featured_photo']); ?>"
                                alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="icons">
                                <a href="product-details.php?p_id=<?php echo $product['p_id']; ?>" class="cart-btn">Add to
                                    cart</a>
                            </div>
                        </div>
                        <div class="content">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="price">
                                ₱<?php echo number_format($product['current_price'], 2); ?>
                                <?php if (!empty($product['old_price'])): ?>
                                    <span>₱<?php echo number_format($product['old_price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No products found for this category.</p>
            <?php endif; ?>

        </div>
    </section>
    <!-- prod sec -->

    <section class="about" id="about">
        <h1 class="heading"><span> about </span> us </h1>

        <div class="row">
            <div class="video-container">
                <video src="../ivd/vid.mp4" loop autoplay muted></video>
                <h3>best flower sellers</h3>
            </div>


            <div class="content">
                <h3>why choose us?</h3>
                <p>Welcome to La Sorpresa by J & B, your trusted flower shop where elegance meets creativity. Founded by
                    two passionate individuals, J & B, our shop was born out of a shared love for flowers and a
                    commitment to spreading joy through nature’s most beautiful creations.

                    At La Sorpresa by J & B, we specialize in crafting bespoke floral arrangements for every
                    occasion—whether it’s a heartfelt surprise, a romantic gesture, or a grand celebration. Each bouquet
                    and design is thoughtfully curated to reflect the emotions and stories behind them, making every
                    arrangement as unique as the person receiving it.</p>
                <p>Our mission is simple: to create meaningful moments through the art of floristry. We source only the
                    freshest and most vibrant blooms, ensuring quality and beauty in every petal. From timeless roses to
                    exotic blooms, we blend creativity and passion to bring your floral visions to life.

                    More than just a flower shop, La Sorpresa by J & B is a celebration of love, life, and surprises.
                    Let us help you make every occasion unforgettable with the perfect arrangement crafted just for you.

                    Surprise them beautifully, with La Sorpresa by J & B.</p>
                <a href="#" class="btn">Learn More!</a>
            </div>
        </div>
    </section>

    <!-- review -->
    <section class="review" id="review">
        <h1 class="heading">Customer's <span>Review</span></h1>
        <div class="box-container">
            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae natus hic ducimus repellat
                    ipsam? Sit laborum labore explicabo earum! Consequuntur molestiae nostrum corrupti nam cum porro
                    repudiandae nihil ut laudantium.</p>
                <div class="user">
                    <img src="../ivd/flower.png" alt="">
                    <div class="user-info">
                        <h3>John Doe</h3>
                        <span>Happy Customer</span>
                    </div>
                    <span class="fas fa-quote-right"></span>
                </div>

            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae natus hic ducimus repellat
                    ipsam? Sit laborum labore explicabo earum! Consequuntur molestiae nostrum corrupti nam cum porro
                    repudiandae nihil ut laudantium.</p>
                <div class="user">
                    <img src="../ivd/flower.png" alt="">
                    <div class="user-info">
                        <h3>John Doe</h3>
                        <span>Happy Customer</span>
                    </div>
                    <span class="fas fa-quote-right"></span>
                </div>

            </div>

            <div class="box">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae natus hic ducimus repellat
                    ipsam? Sit laborum labore explicabo earum! Consequuntur molestiae nostrum corrupti nam cum porro
                    repudiandae nihil ut laudantium.</p>
                <div class="user">
                    <img src="../ivd/flower.png" alt="">
                    <div class="user-info">
                        <h3>John Doe</h3>
                        <span>Happy Customer</span>
                    </div>
                    <span class="fas fa-quote-right"></span>
                </div>

            </div>
        </div>
    </section>
    <!-- review -->

    <div class="footer-basic">
        <footer>
            <div class="social"><a href="https://www.instagram.com/lasorpresaflowershop/"><i class="icon ion-social-instagram"></i></a><a href="https://www.facebook.com/messages/t/105108568239439"><i class="icon ion-android-textsms"></i></a><a href="https://www.facebook.com/lasorpresabyjb"><i class="icon ion-social-facebook"></i></a></div>
            <p class="copyright">La Sorpresa by J & B © 2021</p>
        </footer>
    </div>


</body>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Close the dropdown when clicking outside
    window.onclick = function (event) {
        if (!event.target.matches('.fa-user')) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    };
</script>

</html>