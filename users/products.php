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

      <h3 class="title"> Flower products </h3>

      <?php
// Fetch categories (example query, adjust as per your database structure)
$statement = $pdo->prepare("
    SELECT * 
    FROM end_category t1
    JOIN mid_category t2
    ON t1.mcat_id = t2.mcat_id
    WHERE t1.mcat_id = 3 /* Only get categories for the mid-category with ID 3 */
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
        <a href="#" onclick="filterProducts('all', this)">All</a>
    </li>
    <?php
    // Ensure $result is defined and not empty
    if (!empty($result) && is_array($result)) {
        $limit = 5;
        $count = 0;
        $page = isset($_GET['page']) ? $_GET['page'] : 1; // Track the page number

        // Calculate the starting index for pagination
        $start = ($page - 1) * $limit;

        // Loop through the categories and show only the categories for the current page
        $categoriesToShow = array_slice($result, $start, $limit);
        
        foreach ($categoriesToShow as $row) {
            echo '<li data-filter="' . htmlspecialchars($row['ecat_id']) . '">
                    <a href="#" onclick="filterProducts(\'' . htmlspecialchars($row['ecat_id']) . '\', this)">' . htmlspecialchars($row['ecat_name']) . '</a>
                  </li>';
            $count++;
        }

        // If there are more categories, add the "Next" button
        if ($count == $limit && count($result) > $page * $limit) {
            $nextPage = $page + 1;
            echo '<li class="next-page">
                    <a href="?page=' . $nextPage . '" onclick="loadMoreCategories()">&gt;</a>
                  </li>';
        }

        // If not on the first page, add the "Previous" button
        if ($page > 1) {
            $prevPage = $page - 1;
            echo '<li class="prev-page">
                    <a href="?page=' . $prevPage . '" onclick="loadMoreCategories()">&lt;</a>
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

<?php include('../loading.php'); ?>

<script>
    function filterProducts(ecat_id, element) {
    const container = document.getElementById('productContainer');
    
    // Show loading message
    container.innerHTML = "<p>Loading products...</p>";

    // Determine the correct API endpoint or parameter for 'all'
    const url = ecat_id === 'all' ? 'fetch-products.php' : `fetch-products.php?ecat_id=${ecat_id}`;

    // Update the active class in the indicator
    const indicators = document.querySelectorAll('.indicator li');
    indicators.forEach(indicator => indicator.classList.remove('active')); // Remove active class from all
    if (element) {
        element.parentElement.classList.add('active'); // Add active class to the clicked element
    }

    // Fetch products using AJAX (using Fetch API)
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error fetching products');
            }
            return response.text();
        })
        .then(data => {
            container.innerHTML = data;
        })
        .catch(error => {
            container.innerHTML = "<p>Error loading products. Please try again.</p>";
            console.error('Error fetching products:', error);
        });
}

// Handling the ">" arrow click to load more categories/products
// Handling the ">" arrow click to load more categories/products
function loadMoreProducts() {
    const container = document.getElementById('productContainer');
    container.innerHTML = "<p>Loading more products...</p>";
    
    // URL modification to load more products (make sure your backend is capable of this)
    const url = `fetch-products.php?ecat_id=all&load_more=true`;
    
    fetch(url)
        .then(response => response.text())
        .then(data => {
            container.innerHTML = data; // Display new products
        })
        .catch(error => {
            container.innerHTML = "<p>Error loading more products. Please try again.</p>";
            console.error('Error loading more products:', error);
        });
}



// Load all products by default when the page loads
window.onload = function() {
    filterProducts('all');
};

// Function to open modal
function openModal(productId) {
    // Fetch product data from the selected product using AJAX or embedded data
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch-product-details.php?p_id=' + productId, true);
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
</script>
<script>
    document.getElementById('select').addEventListener('change', function () {
    filterProducts('all');
});

function filterProducts(ecat_id, element) {
    const container = document.getElementById('productContainer');
    const sortOrder = document.getElementById('select').value;

    // Show loading message
    container.innerHTML = "<p>Loading products...</p>";

    // Determine the correct API endpoint
    const url = ecat_id === 'all' 
        ? `fetch-products.php?sort=${sortOrder}` 
        : `fetch-products.php?ecat_id=${ecat_id}&sort=${sortOrder}`;

    // Fetch sorted products
    const xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            container.innerHTML = xhr.responseText;
        } else {
            container.innerHTML = "<p>Error loading products. Please try again.</p>";
        }
    };
    xhr.send();
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