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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
     <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

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

<section>
   <div class="container">

      <h3 class="title"> Occasion </h3>

      <?php
// Fetch categories (example query, adjust as per your database structure)
$statement = $pdo->prepare("
    SELECT * 
    FROM end_category t1
    JOIN mid_category t2
    ON t1.mcat_id = t2.mcat_id
    WHERE t1.mcat_id = 20 /* Only get categories for the mid-category with ID 19 */
    ORDER BY t1.ecat_id ASC
");

$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="filter-condition">
    <select name="" id="select">
        <option value="Default">Default</option>
        <option value="LowToHigh">Low to High</option>
        <option value="HighToLow">High to Low</option>
    </select>
</div>

<!-- Categories List (Categories Filter) -->
<ul class="indicator">
    <li data-filter="all" class="active">
        <a href="#" onclick="filterProducts('all')">All</a>
    </li>
    <?php
    // Ensure $result is defined and not empty
    if (!empty($result) && is_array($result)) {
        foreach ($result as $row) {
            echo '<li data-filter="' . htmlspecialchars($row['ecat_id']) . '">
                    <a href="#" onclick="filterProducts(' . htmlspecialchars($row['ecat_id']) . ')">' . htmlspecialchars($row['ecat_name']) . '</a>
                  </li>';
        }
    } else {
        // Fallback message when no categories are found
        echo '<li>No categories available.</li>';
    }
    ?>
</ul>
<!-- Product Display Area -->
<div class="products-container" id="productContainer">
    <!-- Products will be loaded here dynamically -->
</div>

<!-- Product Modal -->
<!-- Modal for Product Details -->
<div id="productModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="modalImage" src="" alt="Product Image">
        <h2 id="modalName"></h2>
        <p id="modalDescription"></p>
        <div id="modalPrice"></div>
        <button onclick="addToCart()">Add to Cart</button>
    </div>
</div>


   <ul class="listPage">
        <li>Next Page</li>  
   </ul>
</section>


<script>
// Function to filter products by ecat_id
function filterProducts(ecat_id) {
    const container = document.getElementById('productContainer');
    
    // Show loading message
    container.innerHTML = "<p>Loading products...</p>";

    // Determine the correct API endpoint or parameter for 'all'
    const url = ecat_id === 'all' ? 'fetch-occasion.php' : `fetch-occasion.php?ecat_id=${ecat_id}`;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            container.innerHTML = xhr.responseText;
        } else {
            container.innerHTML = "<p>Error loading products. Please try again.</p>";
        }
    };
    xhr.send();
}

window.onload = function() {
    filterProducts('all');
};

function openModal(productId) {
    // Fetch product data from the selected product using AJAX or embedded data
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch-occasion-details.php?p_id=' + productId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const product = JSON.parse(xhr.responseText);

            // Update modal with product details
            document.getElementById('modalImage').src = '../admin/uploads/' + product.featured_photo;
            document.getElementById('modalName').innerText = product.name;
            document.getElementById('modalDescription').innerText = product.description;
            document.getElementById('modalPrice').innerText = "₱" + product.current_price;

            // Show the modal
            document.getElementById('productModal').style.display = 'block';
        } else {
            console.error("Error loading product details, status:", xhr.status);
        }
    };
    xhr.send();
}
// Close modal
function closeModal() {
    document.getElementById('productModal').style.display = 'none';
}

// Add to cart function (to be implemented)
function addToCart() {
    alert("Added to cart!");
}

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