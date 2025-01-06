<?php require_once('header.php'); ?>
<!-- css -->
<link rel="stylesheet" href="../css/product.css">
<script src="../js/product.js" defer></script>

<section>
   <div class="container">

      <h3 class="title"> Flower products </h3>

<!-- Categories List (Categories Filter) -->
<ul class="indicator">
    <li data-filter="all" class="active"><a href="#" onclick="filterProducts('all')">All</a></li>
    <?php
    // Fetch end-level categories
    $statement = $pdo->prepare("SELECT * 
                                FROM end_category t1
                                JOIN mid_category t2
                                ON t1.mcat_id = t2.mcat_id
                                WHERE t1.mcat_id = 3 /* Only get categories for the mid-category with ID 3 */
                                ORDER BY t1.ecat_id ASC");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        echo '<li data-filter="' . htmlspecialchars($row['ecat_id']) . '"><a href="#" onclick="filterProducts(' . $row['ecat_id'] . ')">' . htmlspecialchars($row['ecat_name']) . '</a></li>';
    }
    ?>
</ul>

<!-- Product Display Area -->
<div class="products-container" id="productContainer">
    <!-- Products will be loaded here dynamically -->
</div>

<!-- Product Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">×</span>
        <img id="modalImage" src="" alt="Product Image">
        <h2 id="modalName">Product Name</h2>
        <p id="modalDescription">Product Description</p>
        <p id="modalPrice">$0.00</p>
        <button onclick="addToCart()">Add to Cart</button>
    </div>
</div>



      <div class="filter-condition">
         <select name="" id="select">
            <option value="Default">Default</option>
            <option value="LowToHigh">Low to High</option>
            <option value="HighToLow">High to Low</option>
         </select>
      </div>

   <div class="products-preview">

      <div class="preview" data-target="p-1">
         <i class="fas fa-times"></i>
         <img src="images/1.png" alt="">
         <h3>Flowers</h3>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
            <span>( 250 )</span>
         </div>
         <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur, dolorem.</p>
         <div class="price">$2.00</div>
         <div class="buttons">
            <a href="#" class="buy">buy now</a>
            <a href="#" class="cart">add to cart</a>
         </div>
      </div>
   </div>

   <ul class="listPage">

   </ul>
</section>

<!-- JavaScript to Handle AJAX Requests -->
<script>
// Function to filter products by ecat_id
function filterProducts(ecat_id) {
    const container = document.getElementById('productContainer');
    
    // Show loading message
    container.innerHTML = "<p>Loading products...</p>";

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch-products.php?ecat_id=' + ecat_id, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            container.innerHTML = xhr.responseText;
        } else {
            container.innerHTML = "<p>Error loading products. Please try again.</p>";
        }
    };
    xhr.send();
}

// Function to open modal
function openModal(productId) {
    // Fetch product data from the selected product using AJAX
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch-product-details.php?p_id=' + productId, true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            // Parse the JSON response
            const product = JSON.parse(xhr.responseText);

            // Check if product data is returned
            if (product && product.p_id) {
                document.getElementById('modalImage').src = '../admin/uploads/' + product.featured_photo;
                document.getElementById('modalName').innerText = product.name;
                document.getElementById('modalDescription').innerText = product.description;
                document.getElementById('modalPrice').innerText = "$" + product.current_price.toFixed(2);

                // Show the modal
                document.getElementById('productModal').style.display = 'block';
            } else {
                console.log('Product not found or error in the response');
            }
        } else {
            console.log('Error loading product details');
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
</body>






</html>