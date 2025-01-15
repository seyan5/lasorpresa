<?php
require_once('header.php');

// Handle form submission for adding color
if (isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';
    $success_message = '';

    // Validation: check if the color name is empty
    if (empty($_POST['color_name'])) {
        $valid = 0;
        $error_message .= "Color Name cannot be empty<br>";
    } else {
        // Duplicate Color Name check
        $statement = $pdo->prepare("SELECT * FROM color WHERE color_name=?");
        $statement->execute([$_POST['color_name']]);
        $total = $statement->rowCount();
        if ($total) {
            $valid = 0;
            $error_message .= "Color Name already exists<br>";
        }
    }

    // If validation passed, insert the new color into the database
    if ($valid == 1) {
        $statement = $pdo->prepare("INSERT INTO color (color_name) VALUES (?)");
        $statement->execute([$_POST['color_name']]);
        $success_message = 'Color is added successfully.';
    }

    // Make sure the headers are set to return JSON correctly
    header('Content-Type: application/json');  // Ensure JSON response
    // Ensure only one response is sent.
    if ($valid == 1) {
        echo json_encode(['success' => $success_message]);
    } else {
        echo json_encode(['error' => $error_message]);
    }

    exit();  // Ensure no additional output after the response
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Sorpresa Admin</title>
    <link rel="stylesheet" href="../css/settings.css?v.1.1">
    <link rel="stylesheet" href="../css/products.css">
    
</head>
<body>
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <div class="logo-container">
                            <img src="../images/logo.png" alt="Logo" class="logo" />
                        </div>
                        <span class="title"></span>
                    </a>
                </li>

                <li>
                    <a href="../admin/dashboard.php">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/users.php">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Users</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="chatbubble-outline"></ion-icon>
                        </span>
                        <span class="title">Messages</span>
                    </a>
                </li>

                <li>
                    <a href="product/product.php">
                        <span class="icon">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Products</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/orders/order.php">
                        <span class="icon">
                            <ion-icon name="cart-outline"></ion-icon>
                        </span>
                        <span class="title">Manage Orders</span>
                    </a>
                </li>
                <li>
                    <a href="customize/customize-order.php">
                        <span class="icon">
                        <ion-icon name="color-wand-outline"></ion-icon>
                        </span>
                        <span class="title"> Customize Orders</span>
                    </a>
                </li>

                <li>
                    <a href="#">
                        <span class="icon">
                            <ion-icon name="settings-outline"></ion-icon>
                        </span>
                        <span class="title">Settings</span>
                    </a>
                </li>

                <li>
                    <a href="../index.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>

        <section class="content">
            <div class="row">
                <div class="">
                    <div class="">
                        <div class="">
                            <table>
                                <thead>
                                </thead>
                                <tbody>
                                <div class="horizontal-table">
                                    <a href="settings.php">Color</a>
                                    <a href="settings/container.php">Container</a>
                                    <a href="settings/topcategory.php">Top Level Category</a>
                                    <a href="settings/midcategory.php">Mid Level Category</a>
                                    <a href="settings/endcategory.php">End Level Category</a>
                                </div>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">

            <div class="row">
            <div class="col-md-12">


            <div class="box box-info">
            <h1>Colors</h1>
    <div class="box-body table-responsive table-container">
        <table id="example1" class="table table-bordered table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Color Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 0;
                $statement = $pdo->prepare("SELECT * FROM color ORDER BY color_id ASC");
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);							
                foreach ($result as $row) {
                    $i++;
                    ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $row['color_name']; ?></td>
                        <td>
                            <a href="settings/color-edit.php?id=<?php echo $row['color_id']; ?>" class="btn btn-primary btn-xs">Edit</a>
                            <a href="#" class="btn btn-danger btn-xs" data-href="settings/color-delete.php?id=<?php echo $row['color_id']; ?>" data-toggle="modal" data-target="#confirm-delete">Delete</a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <section class="content-header" style="background-color: white !important;">
    <div class="content-header-right">
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addColorModal">Add New</button>
    </div>
</section>     
</div>
       

        </section>

        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Delete Confirmation</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure want to delete this item?</p>
                        <p style="color:red;">Be careful! This product will be deleted from the order table, payment table, size table, color table and rating table also.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-danger btn-ok">Delete</a>
                    </div>
                </div>
            </div>
</div>
        </section>
    </div>
</body>

<!-- Modal Structure for Add Color -->
<div class="modal fade" id="addColorModal" tabindex="-1" role="dialog" aria-labelledby="addColorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addColorModalLabel">Add New Color</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="colorAddForm">
                    <div class="form-group">
                        <label for="color_name">Color Name <span>*</span></label>
                        <input type="text" class="form-control" id="color_name" name="color_name" required>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                    <div id="errorMessage" class="alert alert-danger d-none"></div>
                    <div id="successMessage" class="alert alert-success d-none"></div>
                </form>
            </div>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('#colorAddForm').on('submit', function(e) {
            e.preventDefault();  // Prevent form submission

            var colorName = $('#color_name').val();

            // Clear previous messages
            $('#errorMessage').addClass('d-none');
            $('#successMessage').addClass('d-none');

            // Validate input (check if color name is empty)
            if (colorName === '') {
                $('#errorMessage').text('Color Name cannot be empty').removeClass('d-none');
                return; // Stop further processing if input is invalid
            }

            // Send AJAX request to add the color
            $.ajax({
                url: 'settings/color-add.php',
                type: 'POST',
                data: { color_name: colorName, form1: true },
                success: function(response) {
                    console.log("Response from server:", response);  // Log response to console

                    try {
                        var data = JSON.parse(response);  // Attempt to parse the JSON response

                        // Check for success or error messages
                        if (data.success) {
                            $('#successMessage').text(data.success).removeClass('d-none');
                            $('#errorMessage').addClass('d-none');
                            $('#color_name').val('');  // Clear input field after success

                            setTimeout(function() {
                                $('#addColorModal').modal('hide');  // Close the modal after a short delay
                                window.location.href = 'settings.php';  // Redirect to settings page
                            }, 1500);
                        } else if (data.error) {
                            $('#errorMessage').text(data.error).removeClass('d-none');
                            $('#successMessage').addClass('d-none');
                        }
                    } catch (error) {
                        // If there's a JSON parsing error, show an error message
                        console.error('JSON parsing error:', error);  // Log the error
                        $('#errorMessage').text('There was an error processing the response. Please try again later.').removeClass('d-none');
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX request failure
                    console.error("AJAX Error:", error);  // Log AJAX error for debugging
                    $('#errorMessage').text('An error occurred: ' + error).removeClass('d-none');
                }
            });
        });
    });
</script>


