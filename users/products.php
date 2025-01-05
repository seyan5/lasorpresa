<?php require_once('header.php'); ?>
<?php
foreach ($result as $row) {
   $name = $row['name'];
   $old_price = $row['old_price'];
   $current_price = $row['current_price'];
   $quantity = $row['quantity'];
   $featured_photo = $row['featured_photo'];
   $description = $row['description'];
   $short_description = $row['short_description'];
   $feature = $row['feature'];
   $condition = $row['condition'];
   $total_view = $row['total_view'];
   $is_featured = $row['is_featured'];
   $is_active = $row['is_active'];
   $ecat_id = $row['ecat_id'];
}

// Getting all categories name for breadcrumb
$statement = $pdo->prepare("SELECT
                        t1.ecat_id,
                        t1.ecat_name,
                        t1.mcat_id,

                        t2.mcat_id,
                        t2.mcat_name,
                        t2.tcat_id,

                        t3.tcat_id,
                        t3.tcat_name

                        FROM end_category t1
                        JOIN mid_category t2
                        ON t1.mcat_id = t2.mcat_id
                        JOIN top_category t3
                        ON t2.tcat_id = t3.tcat_id
                        WHERE t1.ecat_id=?");
$statement->execute(array($ecat_id));
$total = $statement->rowCount();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
   $ecat_name = $row['ecat_name'];
   $mcat_id = $row['mcat_id'];
   $mcat_name = $row['mcat_name'];
   $tcat_id = $row['tcat_id'];
   $tcat_name = $row['tcat_name'];
}


$total_view = $total_view + 1;

$statement = $pdo->prepare("UPDATE product SET total_view=? WHERE p_id=?");
$statement->execute(array($p_total_view, $_REQUEST['id']));


$statement = $pdo->prepare("SELECT * FROM product_type WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
   $type[] = $row['type'];
}

$statement = $pdo->prepare("SELECT * FROM product_color WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$result = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
   $color[] = $row['color_id'];
}


if (isset($_POST['form_review'])) {

   $statement = $pdo->prepare("SELECT * FROM rating WHERE p_id=? AND id=?");
   $statement->execute(array($_REQUEST['id'], $_SESSION['users']['id']));
   $total = $statement->rowCount();

   if ($total) {
      $error_message = LANG_VALUE_68;
   } else {
      $statement = $pdo->prepare("INSERT INTO rating (p_id,id,comment,rating) VALUES (?,?,?,?)");
      $statement->execute(array($_REQUEST['id'], $_SESSION['user']['id'], $_POST['comment'], $_POST['rating']));
      $success_message = LANG_VALUE_163;
   }

}

// Getting the average rating for this product
$t_rating = 0;
$statement = $pdo->prepare("SELECT * FROM rating WHERE p_id=?");
$statement->execute(array($_REQUEST['id']));
$tot_rating = $statement->rowCount();
if ($tot_rating == 0) {
   $avg_rating = 0;
} else {
   $result = $statement->fetchAll(PDO::FETCH_ASSOC);
   foreach ($result as $row) {
      $rating = $rating + $row['rating'];
   }
   $avg_rating = $rating / $tot_rating;
}

if (isset($_POST['form_add_to_cart'])) {

   // getting the currect stock of this product
   $statement = $pdo->prepare("SELECT * FROM product WHERE p_id=?");
   $statement->execute(array($_REQUEST['id']));
   $result = $statement->fetchAll(PDO::FETCH_ASSOC);
   foreach ($result as $row) {
      $current_quantity = $row['quantity'];
   }
   if ($_POST['quantity'] > $current_quantity):
      $temp_msg = 'Sorry! There are only ' . $current_quantity . ' item(s) in stock';
      ?>
      <script type="text/javascript">alert('<?php echo $temp_msg; ?>');</script>
      <?php
   else:
      if (isset($_SESSION['cart_p_id'])) {
         $arr_cart_p_id = array();
         $arr_cart_type_id = array();
         $arr_cart_color_id = array();
         $arr_cart_quantity = array();
         $arr_cart_current_price = array();

         $i = 0;
         foreach ($_SESSION['cart_p_id'] as $key => $value) {
            $i++;
            $arr_cart_p_id[$i] = $value;
         }

         $i = 0;
         foreach ($_SESSION['cart_type_id'] as $key => $value) {
            $i++;
            $arr_cart_type_id[$i] = $value;
         }

         $i = 0;
         foreach ($_SESSION['cart_color_id'] as $key => $value) {
            $i++;
            $arr_cart_color_id[$i] = $value;
         }


         $added = 0;
         if (!isset($_POST['type_id'])) {
            $type_id = 0;
         } else {
            $type_id = $_POST['type_id'];
         }
         if (!isset($_POST['color_id'])) {
            $color_id = 0;
         } else {
            $color_id = $_POST['color_id'];
         }
         for ($i = 1; $i <= count($arr_cart_p_id); $i++) {
            if (($arr_cart_p_id[$i] == $_REQUEST['id']) && ($arr_cart_type_id[$i] == $type_id) && ($arr_cart_color_id[$i] == $color_id)) {
               $added = 1;
               break;
            }
         }
         if ($added == 1) {
            $error_message1 = 'This product is already added to the shopping cart.';
         } else {

            $i = 0;
            foreach ($_SESSION['cart_p_id'] as $key => $res) {
               $i++;
            }
            $new_key = $i + 1;

            if (isset($_POST['type_id'])) {

               $type_id = $_POST['type_id'];

               $statement = $pdo->prepare("SELECT * FROM `type` WHERE type_id=?");
               $statement->execute(array($type_id));
               $result = $statement->fetchAll(PDO::FETCH_ASSOC);
               foreach ($result as $row) {
                  $type_name = $row['type_name'];
               }
            } else {
               $type_id = 0;
               $type_name = '';
            }

            if (isset($_POST['color_id'])) {
               $color_id = $_POST['color_id'];
               $statement = $pdo->prepare("SELECT * FROM color WHERE color_id=?");
               $statement->execute(array($color_id));
               $result = $statement->fetchAll(PDO::FETCH_ASSOC);
               foreach ($result as $row) {
                  $color_name = $row['color_name'];
               }
            } else {
               $color_id = 0;
               $color_name = '';
            }


            $_SESSION['cart_p_id'][$new_key] = $_REQUEST['id'];
            $_SESSION['cart_type_id'][$new_key] = $type_id;
            $_SESSION['cart_type_name'][$new_key] = $type_name;
            $_SESSION['cart_color_id'][$new_key] = $color_id;
            $_SESSION['cart_color_name'][$new_key] = $color_name;
            $_SESSION['cart_quantity'][$new_key] = $_POST['quantity'];
            $_SESSION['cart_current_price'][$new_key] = $_POST['current_price'];
            $_SESSION['cart_name'][$new_key] = $_POST['name'];
            $_SESSION['cart_featured_photo'][$new_key] = $_POST['featured_photo'];

            $success_message1 = 'Product is added to the cart successfully!';
         }

      } else {

         if (isset($_POST['type_id'])) {

            $type_id = $_POST['type_id'];

            $statement = $pdo->prepare("SELECT * FROM `type`` WHERE type_id=?");
            $statement->execute(array($type_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
               $type_name = $row['type_name'];
            }
         } else {
            $type_id = 0;
            $type_name = '';
         }

         if (isset($_POST['color_id'])) {
            $color_id = $_POST['color_id'];
            $statement = $pdo->prepare("SELECT * FROM color WHERE color_id=?");
            $statement->execute(array($color_id));
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row) {
               $color_name = $row['color_name'];
            }
         } else {
            $color_id = 0;
            $color_name = '';
         }


         $_SESSION['cart_p_id'][1] = $_REQUEST['id'];
         $_SESSION['cart_typee_id'][1] = $type_id;
         $_SESSION['cart_type_name'][1] = $type_name;
         $_SESSION['cart_color_id'][1] = $color_id;
         $_SESSION['cart_color_name'][1] = $color_name;
         $_SESSION['cart_quantity'][1] = $_POST['quantity'];
         $_SESSION['cart_current_price'][1] = $_POST['current_price'];
         $_SESSION['cart_name'][1] = $_POST['p_name'];
         $_SESSION['cart_featured_photo'][1] = $_POST['featured_photo'];

         $success_message1 = 'Product is added to the cart successfully!';
      }
   endif;
}
?>

<?php
if ($error_message1 != '') {
   echo "<script>alert('" . $error_message1 . "')</script>";
}
if ($success_message1 != '') {
   echo "<script>alert('" . $success_message1 . "')</script>";
   header('location: product.php?id=' . $_REQUEST['id']);
}
?>
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
                            ORDER BY t1.mcat_id DESC");
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