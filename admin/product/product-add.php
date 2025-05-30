<?php require_once('../header.php');
include('../auth.php'); ?>
<link rel="stylesheet" href="css/prodadd.css?v3">

<?php

$error_message = ''; // Initialize the error_message variable
$success_message = ''; // Initialize the success_message variable

if(isset($_POST['form1'])) {
    $valid = 1;

    if(empty($_POST['tcat_id'])) {
        $valid = 0;
        $error_message .= "You must have to select a top level category<br>";
    }

    if(empty($_POST['name'])) {
        $valid = 0;
        $error_message .= "Product name can not be empty<br>";
    }

    if(empty($_POST['current_price'])) {
        $valid = 0;
        $error_message .= "Current Price can not be empty<br>";
    }

    if(empty($_POST['quantity'])) {
        $valid = 0;
        $error_message .= "Quantity can not be empty<br>";
    }

    $path = $_FILES['featured_photo']['name'];
    $path_tmp = $_FILES['featured_photo']['tmp_name'];

    if($path!='') {
        $ext = pathinfo( $path, PATHINFO_EXTENSION );
        $file_name = basename( $path, '.' . $ext );
        if( $ext!='jpg' && $ext!='png' && $ext!='jpeg' && $ext!='gif' ) {
            $valid = 0;
            $error_message .= 'You must have to upload jpg, jpeg, gif or png file<br>';
        }
    } else {
        $valid = 0;
        $error_message .= 'You must have to select a featured photo<br>';
    }

    if($valid == 1) {

        $statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'product'");
        $statement->execute();
        $result = $statement->fetchAll();
        foreach($result as $row) {
            $ai_id=$row[10];
        }

        if( isset($_FILES['photo']["name"]) && isset($_FILES['photo']["tmp_name"]) )
        {
            $photo = array();
            $photo = $_FILES['photo']["name"];
            $photo = array_values(array_filter($photo));

            $photo_temp = array();
            $photo_temp = $_FILES['photo']["tmp_name"];
            $photo_temp = array_values(array_filter($photo_temp));

            $statement = $pdo->prepare("SHOW TABLE STATUS LIKE 'product_photo'");
            $statement->execute();
            $result = $statement->fetchAll();
            foreach($result as $row) {
                $next_id1=$row[10];
            }
            $z = $next_id1;

            $m=0;
            for($i=0;$i<count($photo);$i++)
            {
                $my_ext1 = pathinfo( $photo[$i], PATHINFO_EXTENSION );
                if( $my_ext1=='jpg' || $my_ext1=='png' || $my_ext1=='jpeg' || $my_ext1=='gif' ) {
                    $final_name1[$m] = $z.'.'.$my_ext1;
                    move_uploaded_file($photo_temp[$i],"../uploads/product_photos/".$final_name1[$m]);
                    $m++;
                    $z++;
                }
            }

            if(isset($final_name1)) {
                for($i=0;$i<count($final_name1);$i++)
                {
                    $statement = $pdo->prepare("INSERT INTO product_photo (photo,p_id) VALUES (?,?)");
                    $statement->execute(array($final_name1[$i],$ai_id));
                }
            }            
        }

        $final_name = 'product-featured-'.$ai_id.'.'.$ext;
        move_uploaded_file( $path_tmp, '../uploads/'.$final_name );

        // Saving data into the main table product
        $statement = $pdo->prepare("INSERT INTO product(
            name,
            old_price,
            current_price,
            quantity,
            featured_photo,
            description,
            is_featured,
            is_active,
            ecat_id
        ) VALUES (?,?,?,?,?,?,?,?,?)");

        $statement->execute(array(
            $_POST['name'],
            $_POST['old_price'],
            $_POST['current_price'],
            $_POST['quantity'],
            $final_name,
            $_POST['description'],
            isset($_POST['is_featured']) ? $_POST['is_featured'] : 0,  // Default to 0 if not set
            isset($_POST['is_active']) ? $_POST['is_active'] : 0,      // Default to 0 if not set
            $_POST['ecat_id']
        ));

        if(isset($_POST['type'])) {
            foreach($_POST['type'] as $value) {
                $statement = $pdo->prepare("INSERT INTO product_type (type_id,p_id) VALUES (?,?)");
                $statement->execute(array($value,$ai_id));
            }
        }

        if(isset($_POST['color'])) {
            foreach($_POST['color'] as $value) {
                $statement = $pdo->prepare("INSERT INTO product_color (color_id,p_id) VALUES (?,?)");
                $statement->execute(array($value,$ai_id));
            }
        }

        $success_message = 'Product is added successfully.';
    }
}
?>

<div class="container">
    <section class="content-header">
        <div class="content-header-left">
            <h1>Add Product</h1>
        </div>
        <div class="content-header-right">
            <a href="product.php" class="btn btn-primary btn-sm">View All</a>
        </div>
    </section>

    <section class="content">

        <div class="row">
            <div class="col-md-12">

                <?php if($error_message): ?>
                <div class="callout callout-danger">
                    <p>
                    <?php echo $error_message; ?>
                    </p>
                </div>
                <?php endif; ?>

                <?php if($success_message): ?>
                <div class="callout callout-success">
                    <p><?php echo $success_message; ?></p>
                </div>
                <?php endif; ?>

                <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">

                    <div class="box box-info">
                        <div class="box-body">
                            <div class="top">
                                <label for="" class="col-sm-3 control-label">Top Level Category Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <select id="tcat_id" name="tcat_id" class="form-control select2 top-cat">
                                        <option value="">Select Top Level Category</option>
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM top_category ORDER BY tcat_name ASC");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            ?>
                                            <option value="<?php echo $row['tcat_id']; ?>"><?php echo $row['tcat_name']; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mid">
                                <label for="" class="col-sm-3 control-label">Mid Level Category Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <select id="mcat_id" name="mcat_id" class="form-control select2 mid-cat">
                                        <option value="">Select Mid Level Category</option>
                                    </select>
                                </div>
                            </div>

                            <div class="end">
                                <label for="" class="col-sm-3 control-label">End Level Category Name <span></span></label>
                                <div class="col-sm-4">
                                    <select id="ecat_id" name="ecat_id" class="form-control select2 end-cat">
                                        <option value="">Select End Level Category</option>
                                    </select>
                                </div>
                            </div>

                            <div class="prodname">
                                <label for="" class="col-sm-3 control-label">Product Name <span>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" name="name" class="form-control">
                                </div>
                            </div>  
                            <div class="oprice">
                                <label for="" class="col-sm-3 control-label">Old Price <br><span style="font-type:10px;font-weight:normal;">(In PHP)</span></label>
                                <div class="col-sm-4">
                                    <input type="text" name="old_price" class="form-control">
                                </div>
                            </div>
                            <div class="cprice">
                                <label for="" class="col-sm-3 control-label">Current Price <span>*</span><br><span style="font-type:10px;font-weight:normal;">(In PHP)</span></label>
                                <div class="col-sm-4">
                                    <input type="text" name="current_price" class="form-control">
                                </div>
                            </div>  
                            <div class="quan">
                                <label for="" class="col-sm-3 control-label">Quantity <span>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" name="quantity" class="form-control">
                                </div>
                            </div>

                            <div class="photo">
                                <label for="" class="col-sm-3 control-label">Featured Photo <span>*</span></label>
                                <div class="col-sm-4" style="padding-top:4px;">
                                    <input type="file" name="featured_photo">
                                </div>
                            </div>
                            
                            <div class="des">
                                <label for="" class="col-sm-3 control-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea name="description" class="form-control" cols="30" rows="10" id="editor1"></textarea>
                                </div>
                            </div>

                            <div class="feats">
                                <label for="" class="col-sm-3 control-label">Is Featured?</label>
                                <div class="col-sm-8">
                                    <select name="is_featured" class="form-control" style="width:auto;">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select> 
                                </div>
                            </div>
                            <div class="active">
                                <label for="" class="col-sm-3 control-label">Is Active?</label>
                                <div class="col-sm-8">
                                    <select name="is_active" class="form-control" style="width:auto;">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select> 
                                </div>
                            </div>
                            <div class="addprod">
                                <label for="" class="col-sm-3 control-label"></label>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-success pull-left" name="form1">Add Product</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </section>
</div>
