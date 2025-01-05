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
    // Fetch mid-level categories that belong to the top category with ID 3
    $statement = $pdo->prepare("SELECT * 
                                FROM mid_category t1
                                JOIN top_category t2
                                ON t1.tcat_id = t2.tcat_id
                                WHERE t2.tcat_id = 3  /* Only get categories for the top category with ID 3 */
                                ORDER BY t1.mcat_id ASC");
    $statement->execute();
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

   <?php
// Fetch products for mcat_id = 3 (Regular Flowers)
$statement_flowers = $pdo->prepare("SELECT * 
                                    FROM products p
                                    JOIN mid_category m
                                    ON p.mcat_id = m.mcat_id
                                    WHERE m.mcat_id = 3");  // Regular flowers
$statement_flowers->execute();
$flowers = $statement_flowers->fetchAll(PDO::FETCH_ASSOC);

// Fetch products for mcat_id = 4 (Money Bouquets)
$statement_money_bouquets = $pdo->prepare("SELECT * 
                                           FROM products p
                                           JOIN mid_category m
                                           ON p.mcat_id = m.mcat_id
                                           WHERE m.mcat_id = 8");  // Money bouquets
$statement_money_bouquets->execute();
$money_bouquets = $statement_money_bouquets->fetchAll(PDO::FETCH_ASSOC);
?>
 
 <div class="products-container">
    <!-- Display Regular Flowers (mcat_id = 3) -->
    <h2>Regular Flowers</h2>
    <?php foreach ($flowers as $flower): ?>
        <div class="product" data-name="<?php echo htmlspecialchars($flower['p_id']); ?>">
            <img src="../ivd/<?php echo htmlspecialchars($flower['featured_photo']); ?>" alt="">
            <h3><?php echo htmlspecialchars($flower['name']); ?></h3>
            <div class="price">$<?php echo htmlspecialchars($flower['current_price']); ?></div>
        </div>
    <?php endforeach; ?>

    <!-- Display Money Bouquets (mcat_id = 4) -->
    <h2>Money Bouquets</h2>
    <?php foreach ($money_bouquets as $money_bouquet): ?>
        <div class="product" data-name="<?php echo htmlspecialchars($money_bouquet['p_id']); ?>">
            <img src="../ivd/<?php echo htmlspecialchars($money_bouquet['featured_photo']); ?>" alt="">
            <h3><?php echo htmlspecialchars($money_bouquet['name']); ?></h3>
            <div class="price">$<?php echo htmlspecialchars($money_bouquet['current_price']); ?></div>
        </div>
    <?php endforeach; ?>
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