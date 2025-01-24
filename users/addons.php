<?php 
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");
?>
<?php include('navuser.php'); ?>
<link rel="stylesheet" href="../css/navhead.css"> 

<section>
   <div class="container">

      <h3 class="title"> Addons </h3>

      <?php
// Fetch categories (example query, adjust as per your database structure)
$statement = $pdo->prepare("
    SELECT * 
    FROM end_category t1
    JOIN mid_category t2
    ON t1.mcat_id = t2.mcat_id
    WHERE t1.mcat_id = 19 /* Only get categories for the mid-category with ID 19 */
    ORDER BY t1.ecat_id ASC
");

$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="filter-condition">
    <select name="" id="select">
        <option value="Default">Sort Price</option>
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
        <li>Next Page </li>
   </ul>
</section>
<?php include('footers.php'); ?>
<?php include('../loading.php'); ?>

<script>
// Function to filter products by ecat_id
function filterProducts(ecat_id) {
    const container = document.getElementById('productContainer');
    
    // Show loading message
    container.innerHTML = "<p>Loading products...</p>";

    // Determine the correct API endpoint or parameter for 'all'
    const url = ecat_id === 'all' ? 'addons-fetch.php' : `addons-fetch.php?ecat_id=${ecat_id}`;

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
    xhr.open('GET', 'addons-fetch-details.php?p_id=' + productId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const product = JSON.parse(xhr.responseText);

            // Update modal with product details
            document.getElementById('modalImage').src = '../admin/uploads/' + product.featured_photo;
            document.getElementById('modalName').innerText = product.name;
            document.getElementById('modalDescription').innerText = product.description;
            document.getElementById('modalPrice').innerText = "$" + product.current_price;

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