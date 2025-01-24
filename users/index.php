<!-- This is main configuration File -->
<?php
ob_start();
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");

if (isset($_SESSION['customer'])) {
    echo "Welcome, " . $_SESSION['customer']['cust_name'];
} else {
    echo "You are not logged in.";
}

?>
<?php include('navuser.php'); ?>
    <!-- lheader -->

    <section class="home" id="home">
        <div class="content">
            <h3 style="font-family: Yeseva One, serif;">Say it</h3>
            <h3 style="font-family: Yeseva One, serif;">With</h3>
            <h3 style="font-family: Yeseva One, serif;">Flowers</h3>
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
        
        <!-- <div class="search-container">
    <input type="text" id="search-input" placeholder="What are you looking for?" class="search-input">
    <button class="search-button"><i class="fas fa-search"></i></button>
    <div id="search-results"></div>
</div> -->
 <!-- Where the results will be shown -->

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
                <h3>Best flower sellers</h3>
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
    <h1 class="heading">
        <a href="review.php" style="color: inherit; text-decoration: none;">
            Customer's <span>Review</span>
        </a>
    </h1>
    <div class="box-container">
        <?php
        try {
            // Fetch the 3 most recent reviews from the database
            $stmt = $pdo->prepare("SELECT r.review_id, r.review, r.rating, r.created_at, p.p_id, p.name AS product_name, p.featured_photo, c.cust_name 
                                   FROM reviews r
                                   JOIN product p ON r.product_id = p.p_id
                                   JOIN customer c ON r.customer_id = c.cust_id
                                   ORDER BY r.created_at DESC
                                   LIMIT 3"); // Ensure the query limits to 3

            $stmt->execute();
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Loop through each review and display
            foreach ($reviews as $review):
        ?>
            <div class="box">
                <div class="product-photo">
                    <!-- Display product photo -->
                    <img src="../admin/uploads/<?php echo htmlspecialchars($review['featured_photo']); ?>" alt="Product Image" style="width: 100px; height: 100px;">
                </div>
                <div class="stars">
                    <?php 
                    // Dynamically show stars based on the rating
                    for ($i = 0; $i < 5; $i++) {
                        if ($i < $review['rating']) {
                            echo '<i class="fas fa-star"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';  // Empty star for remaining rating
                        }
                    }
                    ?>
                </div>
                <p><?php echo htmlspecialchars($review['review']); ?></p>
                <div class="user">
                    <img src="../ivd/flower.png" alt="">
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($review['cust_name']); ?></h3>
                        <span>Happy Customer</span>
                    </div>
                    <span class="fas fa-quote-right"></span>
                </div>
            </div>
        <?php
            endforeach;
        } catch (PDOException $e) {
            echo "Error fetching reviews: " . $e->getMessage();
        }
        ?>
    </div>
</section>


    <!-- review -->

    <?php include('footers.php'); ?>
    <?php include('../loading.php'); ?>
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

<script>
    // Toggle the user dropdown when clicking the user icon
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Toggle the notifications dropdown when clicking the bell icon
    function toggleNotificationDropdown() {
        const notificationDropdown = document.getElementById('notificationDropdown');
        notificationDropdown.classList.toggle('show');
    }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function (event) {
        // Close user dropdown if clicked outside
        if (!event.target.matches('.fa-user')) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }

        // Close notifications dropdown if clicked outside
        if (!event.target.matches('.fa-bell')) {
            const notificationDropdown = document.getElementById('notificationDropdown');
            if (notificationDropdown && notificationDropdown.classList.contains('show')) {
                notificationDropdown.classList.remove('show');
            }
        }
    };
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if ("Notification" in window) {
            // Request notification permission if not granted
            Notification.requestPermission().then(permission => {
                if (permission !== "granted") {
                    console.log("Notification permission denied.");
                }
            });

            // Check payment and shipping statuses and schedule notifications
            var paymentUpdates = [];
            
            <?php foreach ($payments as $payment): ?>
                var paymentStatus = '<?php echo $payment['payment_status']; ?>';
                var shippingStatus = '<?php echo $payment['shipping_status']; ?>';
                var paymentId = '<?php echo $payment['name']; ?>';

                if (paymentStatus === 'pending' || shippingStatus !== 'delivered') {
                    paymentUpdates.push({
                        id: paymentId,
                        paymentStatus: paymentStatus,
                        shippingStatus: shippingStatus
                    });
                }
            <?php endforeach; ?>

            // Function to show notification
            function showPaymentShippingNotification(payment) {
                const options = {
                    body: `Payment Status: ${payment.paymentStatus}, Shipping Status: ${payment.shippingStatus}`,
                    icon: '../images/logo.png',
                    tag: `payment-shipping-${payment.id}`
                };
                new Notification(`Product ${payment.id}`, options);
            }

            // Display notifications at intervals (2 seconds)
            if (paymentUpdates.length > 0 && Notification.permission === "granted") {
                let index = 0;
                const notificationInterval = setInterval(() => {
                    if (index < paymentUpdates.length) {
                        showPaymentShippingNotification(paymentUpdates[index]);
                        index++;
                    } else {
                        clearInterval(notificationInterval); // Stop interval after all notifications
                    }
                }, 2000); // 2 seconds interval
            }
        }
    });
</script>




<script>
    // Get the search input field and the results container
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');

    // Add event listener to search input
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim();

        if (query.length > 0) {
            // Create a new AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'search.php?search=' + encodeURIComponent(query), true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Display the search results inside the search-results div
                    searchResults.innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        } else {
            // Clear the search results when input is empty
            searchResults.innerHTML = '';
        }
    });
</script>

<style>
    /* Notification Dropdown */
.notification-dropdown .dropdown-menu {
    display: none;
    position: absolute;
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
    padding: 10px;
    z-index: 1;
}

.notification-dropdown .dropdown-menu.show {
    display: block;
}

.notification-dropdown .dropdown-item {
    display: flex;
    align-items: center;
}

.notification-dropdown .badge {
    margin-left: 5px;
}

.notification-dropdown i {
    margin-right: 10px;
    padding: 5px;
    border-radius: 50%;
}

.notification-dropdown .text-muted {
    font-size: 12px;
}

</style>
</html>