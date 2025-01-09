<!-- This is main configuration File -->
<?php
require_once('header.php'); 

// Check if the search form is submitted
$search_results = [];
if (isset($_POST['search_query']) && !empty($_POST['search_query'])) {
    $search_query = htmlspecialchars($_POST['search_query']);

    // Prepare SQL statement to search in the products table
    $stmt = $pdo->prepare("SELECT * FROM product WHERE name LIKE :search_query");
    $stmt->execute(['search_query' => "%" . $search_query . "%"]);

    // Fetch matching results
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- font -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- css -->
    <link rel="stylesheet" href="../css/main.css">
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
        <a href="products.php">Flowers</a>
        <a href="occasion.php">Occasion</a>
        <a href="addons.php">Addons</a>
        <a href="#review">Review</a>
        <a href="#contacts">Contacts</a>
        <a href="customize.php">Customize</a>

    </nav>
     
    <div class="icons">
    <a href="#" class="fas fa-heart"></a>
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
    
    <!-- lheader -->

    <section class="home" id="home">
        <div class="content">
            <h3>Buy your</h3>
            <h3>Flowers</h3>
            <h3>for your Love</h3>
            <h3>Ones</h3>
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



        <!-- HTML Search Form -->
<div class="search-container">
    <form method="POST" action="">
        <input type="text" name="search_query" placeholder="What are you looking for?" class="search-input">
        <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
    </form>
</div>

<!-- Display Search Results -->
<?php if (!empty($search_results)): ?>
    <h3>Search Results:</h3>
    <div class="search-results">
        <?php foreach ($search_results as $product): ?>
            <div class="product-item">
                <h4><?php echo htmlspecialchars($product['product_name']); ?></h4>
                <p><?php echo htmlspecialchars($product['product_description']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php elseif (isset($search_query)): ?>
    <p>No products found matching "<?php echo htmlspecialchars($search_query); ?>"</p>
<?php endif; ?>
    </section>

   

    <!-- prod sec -->
<section class="products" id="products">

<?php
// Fetch products for mid-category ID = 3 and is_featured = 1
$statement = $pdo->prepare("
    SELECT p.p_id, p.name, p.featured_photo, p.current_price, p.old_price 
    FROM product p
    JOIN end_category ec ON p.ecat_id = ec.ecat_id
    WHERE ec.mcat_id = 3 AND p.is_active = 1 AND p.is_featured = 1
    ORDER BY p.p_id ASC
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
        <a href="products.php" class="btn">See more -></a>
    </div>

    <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
            <div class="box">
                <span class="discount">-10%</span>
                <div class="image">
                    <img src="../admin/uploads/<?php echo htmlspecialchars($product['featured_photo']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="icons">
                        <!-- Add to Cart Button -->
                        <a href="product-details.php?p_id=<?php echo $product['p_id']; ?>" class="cart-btn">Add to cart</a>
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
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsum fugit, nulla similique eius ipsa reiciendis. A delectus commodi fugiat. Vitae, voluptatum sequi. Itaque omnis facere harum nostrum beatae maiores delectus.</p>
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Voluptatem, temporibus tempore aliquam cum laboriosam atque nostrum expedita perspiciatis aut, veniam sequi deserunt, ducimus qui! Quasi officia velit aperiam quia sit!</p>
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
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae natus hic ducimus repellat ipsam? Sit laborum labore explicabo earum! Consequuntur molestiae nostrum corrupti nam cum porro repudiandae nihil ut laudantium.</p>
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
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae natus hic ducimus repellat ipsam? Sit laborum labore explicabo earum! Consequuntur molestiae nostrum corrupti nam cum porro repudiandae nihil ut laudantium.</p>
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
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repudiandae natus hic ducimus repellat ipsam? Sit laborum labore explicabo earum! Consequuntur molestiae nostrum corrupti nam cum porro repudiandae nihil ut laudantium.</p>
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


</body>

<style>
    .user-dropdown {
    position: relative;
    display: inline-block;
}

.fas.fa-user {
    cursor: pointer;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    width: 200px;
    z-index: 1000;
}

.dropdown-menu p {
    margin: 10px;
    font-weight: bold;
}

.dropdown-menu hr {
    margin: 5px 0;
}

.dropdown-menu a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #333;
}

.dropdown-menu a:hover {
    background-color: #f0f0f0;
}

.dropdown-menu.show {
    display: block;
}

</style>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Close the dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.fa-user')) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    };
</script>

</html>