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
    // Replace `specific_tcat_id` with the ID of the desired top category
    $specific_tcat_id = 3; // Example: Top Category ID = 1

    // Fetch mid-level categories for the specific top category
    $statement = $pdo->prepare("SELECT * 
                                FROM mid_category t1
                                JOIN top_category t2
                                ON t1.tcat_id = t2.tcat_id
                                WHERE t1.tcat_id = ? 
                                ORDER BY t1.mcat_id DESC");
    $statement->execute([$specific_tcat_id]);
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($result as $row) {
        echo '<li data-filter="' . htmlspecialchars($row['mcat_name']) . '"><a href="#">' . htmlspecialchars($row['mcat_name']) . '</a></li>';
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
    <?php
    // Replace 'specific_mcat_id' with the mid-category ID you want to filter by
    $specific_mcat_id = isset($_GET['mcat_id']) ? intval($_GET['mcat_id']) : null;

    // Fetch products based on the selected mid-category
    $query = "SELECT * FROM product";
    $params = [];
    if ($specific_mcat_id) {
        $query .= " WHERE mcat_id = ?";
        $params[] = $specific_mcat_id;
    }
    $query .= " ORDER BY name ASC"; // Order by product name or any other criteria

    $statement = $pdo->prepare($query);
    $statement->execute($params);
    $products = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Display products
    if ($products) {
        foreach ($products as $product) {
            $imagePath = "admin/upload/product_photos/" . htmlspecialchars($product['image_path']);
            echo '
            <div class="product" data-category="' . htmlspecialchars($product['category_name']) . '" data-name="p-' . htmlspecialchars($product['id']) . '">
                <img src="' . $imagePath . '" alt="' . htmlspecialchars($product['name']) . '">
                <h3>' . htmlspecialchars($product['name']) . '</h3>
                <div class="price">$' . htmlspecialchars($product['price']) . '</div>
            </div>';
        }
    } else {
        echo '<p>No products found for this category.</p>';
    }
    ?>
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
 
       <div class="product" data-name="p-4">
          <img src="../ivd/flower1.jpg" alt="">
          <h3>Flower1</h3>
          <div class="price">$1.00</div>
       </div>
 
       <div class="product" data-name="p-5">
          <img src="../ivd/flower2.jpg" alt="">
          <h3>Flower2</h3>
          <div class="price">$2.00</div>
       </div>
 
       <div class="product" data-name="p-6">
          <img src="../ivd/flower3.jpg" alt="">
          <h3>Flower3</h3>
          <div class="price">$3.00</div>
       </div>

       <div class="product" data-name="p-6">
          <img src="../ivd/flower1.jpg" alt="">
          <h3>Flower1</h3>
          <div class="price">$1.00</div>
       </div>

       <div class="product" data-name="p-6">
          <img src="../ivd/flower2.jpg" alt="">
          <h3>Flower2</h3>
          <div class="price">$2.00</div>
        </div>

         <div class="product" data-name="p-6">
            <img src="../ivd/flower3.jpg" alt="">
            <h3>Flower3</h3>
            <div class="price">$3.00</div>
         </div>

         <div class="product" data-name="p-4">
            <img src="../ivd/flower.png" alt="">
            <h3>Flower</h3>
            <div class="price">$0.00</div>
         </div>

         <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>

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
    
          <div class="product" data-name="p-4">
             <img src="../ivd/flower1.jpg" alt="">
             <h3>Flower1</h3>
             <div class="price">$1.00</div>
          </div>
    
          <div class="product" data-name="p-5">
             <img src="../ivd/flower2.jpg" alt="">
             <h3>Flower2</h3>
             <div class="price">$2.00</div>
          </div>
    
          <div class="product" data-name="p-6">
             <img src="../ivd/flower3.jpg" alt="">
             <h3>Flower3</h3>
             <div class="price">$3.00</div>
          </div>
   
          <div class="product" data-name="p-6">
             <img src="../ivd/flower1.jpg" alt="">
             <h3>Flower1</h3>
             <div class="price">$1.00</div>
          </div>
   
          <div class="product" data-name="p-6">
             <img src="../ivd/flower2.jpg" alt="">
             <h3>Flower2</h3>
             <div class="price">$2.00</div>
           </div>
   
            <div class="product" data-name="p-6">
               <img src="../ivd/flower3.jpg" alt="">
               <h3>Flower3</h3>
               <div class="price">$3.00</div>
            </div>
   
            <div class="product" data-name="p-4">
               <img src="../ivd/flower.png" alt="">
               <h3>Flower</h3>
               <div class="price">$0.00</div>
            </div>
   
            <div class="product" data-name="p-6">
               <img src="../ivd/flower1.jpg" alt="">
               <h3>Flower1</h3>
               <div class="price">$1.00</div>
            </div>
     
            <div class="product" data-name="p-6">
               <img src="../ivd/flower2.jpg" alt="">
               <h3>Flower2</h3>
               <div class="price">$2.00</div>
             </div>
     
              <div class="product" data-name="p-6">
                 <img src="../ivd/flower3.jpg" alt="">
                 <h3>Flower3</h3>
                 <div class="price">$3.00</div>
              </div>
     
              <div class="product" data-name="p-4">
                 <img src="../ivd/flower.png" alt="">
                 <h3>Flower</h3>
                 <div class="price">$0.00</div>
              </div>
      
              
              <div class="product" data-name="p-6">
               <img src="../ivd/flower1.jpg" alt="">
               <h3>Flower1</h3>
               <div class="price">$1.00</div>
            </div>
     
            <div class="product" data-name="p-6">
               <img src="../ivd/flower2.jpg" alt="">
               <h3>Flower2</h3>
               <div class="price">$2.00</div>
             </div>
     
              <div class="product" data-name="p-6">
                 <img src="../ivd/flower3.jpg" alt="">
                 <h3>Flower3</h3>
                 <div class="price">$3.00</div>
              </div>
     
              <div class="product" data-name="p-4">
                 <img src="../ivd/flower.png" alt="">
                 <h3>Flower</h3>
                 <div class="price">$0.00</div>
              </div>
      
              
              <div class="product" data-name="p-6">
               <img src="../ivd/flower1.jpg" alt="">
               <h3>Flower1</h3>
               <div class="price">$1.00</div>
            </div>
     
            <div class="product" data-name="p-6">
               <img src="../ivd/flower2.jpg" alt="">
               <h3>Flower2</h3>
               <div class="price">$2.00</div>
             </div>
     
              <div class="product" data-name="p-6">
                 <img src="../ivd/flower3.jpg" alt="">
                 <h3>Flower3</h3>
                 <div class="price">$3.00</div>
              </div>
     
              <div class="product" data-name="p-4">
                 <img src="../ivd/flower.png" alt="">
                 <h3>Flower</h3>
                 <div class="price">$0.00</div>
              </div>
      
              
              <div class="product" data-name="p-6">
               <img src="../ivd/flower1.jpg" alt="">
               <h3>Flower1</h3>
               <div class="price">$1.00</div>
            </div>
     
            <div class="product" data-name="p-6">
               <img src="../ivd/flower2.jpg" alt="">
               <h3>Flower2</h3>
               <div class="price">$2.00</div>
             </div>
     
              <div class="product" data-name="p-6">
                 <img src="../ivd/flower3.jpg" alt="">
                 <h3>Flower3</h3>
                 <div class="price">$3.00</div>
              </div>
     
              <div class="product" data-name="p-4">
                 <img src="../ivd/flower.png" alt="">
                 <h3>Flower</h3>
                 <div class="price">$0.00</div>
              </div>
      
              
              <div class="product" data-name="p-6">
               <img src="../ivd/flower1.jpg" alt="">
               <h3>Flower1</h3>
               <div class="price">$1.00</div>
            </div>
     
            <div class="product" data-name="p-6">
               <img src="../ivd/flower2.jpg" alt="">
               <h3>Flower2</h3>
               <div class="price">$2.00</div>
             </div>
     
              <div class="product" data-name="p-6">
                 <img src="../ivd/flower3.jpg" alt="">
                 <h3>Flower3</h3>
                 <div class="price">$3.00</div>
              </div>
     
              <div class="product" data-name="p-4">
                 <img src="../ivd/flower.png" alt="">
                 <h3>Flower</h3>
                 <div class="price">$0.00</div>
              </div>
      
              
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
 
       <div class="product" data-name="p-4">
          <img src="../ivd/flower1.jpg" alt="">
          <h3>Flower1</h3>
          <div class="price">$1.00</div>
       </div>
 
       <div class="product" data-name="p-5">
          <img src="../ivd/flower2.jpg" alt="">
          <h3>Flower2</h3>
          <div class="price">$2.00</div>
       </div>
 
       <div class="product" data-name="p-6">
          <img src="../ivd/flower3.jpg" alt="">
          <h3>Flower3</h3>
          <div class="price">$3.00</div>
       </div>

       <div class="product" data-name="p-6">
          <img src="../ivd/flower1.jpg" alt="">
          <h3>Flower1</h3>
          <div class="price">$1.00</div>
       </div>

       <div class="product" data-name="p-6">
          <img src="../ivd/flower2.jpg" alt="">
          <h3>Flower2</h3>
          <div class="price">$2.00</div>
        </div>

         <div class="product" data-name="p-6">
            <img src="../ivd/flower3.jpg" alt="">
            <h3>Flower3</h3>
            <div class="price">$3.00</div>
         </div>

         <div class="product" data-name="p-4">
            <img src="../ivd/flower.png" alt="">
            <h3>Flower</h3>
            <div class="price">$0.00</div>
         </div>

         <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
   
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
           </div>
           
           <div class="product" data-name="p-6">
            <img src="../ivd/flower1.jpg" alt="">
            <h3>Flower1</h3>
            <div class="price">$1.00</div>
         </div>
  
         <div class="product" data-name="p-6">
            <img src="../ivd/flower2.jpg" alt="">
            <h3>Flower2</h3>
            <div class="price">$2.00</div>
          </div>
  
           <div class="product" data-name="p-6">
              <img src="../ivd/flower3.jpg" alt="">
              <h3>Flower3</h3>
              <div class="price">$3.00</div>
           </div>
  
           <div class="product" data-name="p-4">
              <img src="../ivd/flower.png" alt="">
              <h3>Flower</h3>
              <div class="price">$0.00</div>
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
</html>