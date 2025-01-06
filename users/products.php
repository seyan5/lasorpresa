<?php require_once('header.php'); ?>
<!-- css -->
<link rel="stylesheet" href="../css/product.css">
<script src="../js/product.js" defer></script>

<section>
   <div class="container">

      <h3 class="title"> Flower products </h3>

      <ul class="indicator">
    <li data-filter="all" class="active"><a href="#">All</a></li>
    <?php
    // Fetch end-level categories that belong to mid-category with ID 3
    $statement = $pdo->prepare("SELECT * 
                                FROM end_category t1
                                JOIN mid_category t2
                                ON t1.mcat_id = t2.mcat_id
                                WHERE t1.mcat_id = 3 /* Only get categories for the mid-category with ID 3 */
                                ORDER BY t1.ecat_id ASC");
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        echo '<li data-filter="' . htmlspecialchars($row['ecat_id']) . '"><a href="#">' . htmlspecialchars($row['ecat_name']) . '</a></li>';
    }
    ?>
</ul>


      <div class="filter-condition">
         <select name="" id="select">
            <option value="Default">Default</option>
            <option value="LowToHigh">Low to High</option>
            <option value="HighToLow">High to Low</option>
         </select>
      </div>

      <div class="products-container">
    <div class="product" data-ecat="7" data-name="p-4">
        <img src="../ivd/flower.png" alt="">
        <h3>Flower</h3>
        <div class="price">$0.00</div>
    </div>
    <div class="product" data-ecat="8" data-name="p-1">
        <img src="../ivd/flower1.jpg" alt="">
        <h3>Flower1</h3>
        <div class="price">$1.00</div>
    </div>
</div>

      <div class="products-container">

         <div class="product" data-name="p-4">
            <img src="../ivd/flower.png" alt="">
            <h3>Flower</h3>
            <div class="price">$0.00</div>
         </div>

         <div class="product" data-name="p-1">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>

         <div class="product" data-name="p-2">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
         </div>

         <div class="product" data-name="p-3">
            <img src="../ivd/flower3.jpg" alt="">
            <h3>Flower3</h3>
            <div class="price">$3.00</div>
         </div>



      </div>

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

      <div class="preview" data-target="p-2">
         <i class="fas fa-times"></i>
         <img src="images/2.png" alt="">
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

      <div class="preview" data-target="p-3">
         <i class="fas fa-times"></i>
         <img src="images/3.png" alt="">
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

      <div class="preview" data-target="p-4">
         <i class="fas fa-times"></i>
         <img src="images/4.png" alt="">
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

      <div class="preview" data-target="p-5">
         <i class="fas fa-times"></i>
         <img src="images/5.png" alt="">
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

      <div class="preview" data-target="p-6">
         <i class="fas fa-times"></i>
         <img src="images/6.png" alt="">
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

</body>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get all category filter buttons
        const filterButtons = document.querySelectorAll('.indicator li');

        // Add click event listener to each filter button
        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Get the data-filter value (ecat_id)
                const ecatId = this.getAttribute('data-filter');
                
                // Get all products
                const products = document.querySelectorAll('.products-container .product');

                // Show/Hide products based on the filter
                products.forEach(product => {
                    // Show all if "all" is selected
                    if (ecatId === 'all') {
                        product.style.display = 'block';
                    } else if (product.getAttribute('data-ecat') === ecatId) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });

                // Update active class
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>


</html>