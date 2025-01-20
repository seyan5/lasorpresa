<?php
require_once('../header.php');
require_once '../auth.php';


if(isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';
    $success_message = '';

    // Validation: check if the color name is empty
    if(empty($_POST['color_name'])) {
        $valid = 0;
        $error_message .= "Color Name cannot be empty<br>";
    } else {
        // Duplicate Color Name check
        $statement = $pdo->prepare("SELECT * FROM color WHERE color_name=?");
        $statement->execute(array($_POST['color_name']));
        $total = $statement->rowCount();
        if($total) {
            $valid = 0;
            $error_message .= "Color Name already exists<br>";
        }
    }

    if($valid == 1) {
        // Saving data into the color table
        $statement = $pdo->prepare("INSERT INTO color (color_name) VALUES (?)");
        $statement->execute(array($_POST['color_name']));
        $success_message = 'Color is added successfully.';
    }

    // Return response as JSON (AJAX)
    if(isset($_POST['form1']) && !empty($_POST['form1'])) {
        if($valid == 1) {
            echo json_encode(['success' => $success_message]);
        } else {
            echo json_encode(['error' => $error_message]);
        }
        exit();
    }
}
?>

<!-- Regular HTML content (form part) -->
<section class="content-header">
    <div class="content-header-left">
        <h1>Add Color</h1>
    </div>
    <div class="content-header-right">
        <a href="../settings.php" class="btn btn-primary btn-sm">View All</a>
    </div>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- Handle regular errors and success message -->
            <div id="errorMessage" class="callout callout-danger d-none"></div>
            <div id="successMessage" class="callout callout-success d-none"></div>

            <!-- The form for adding color -->
            <form class="form-horizontal" id="addColorForm">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Color Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="color_name" name="color_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success pull-left">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

<script>
    $(document).ready(function() {
        $('#addColorForm').on('submit', function(e) {
            e.preventDefault();

            var colorName = $('#color_name').val();
            
            // Clear any previous messages
            $('#errorMessage').addClass('d-none');
            $('#successMessage').addClass('d-none');

            // Validate input
            if (colorName === '') {
                $('#errorMessage').text('Color Name cannot be empty').removeClass('d-none');
                return;
            }

            // AJAX request to add the color
            $.ajax({
                url: 'add-color.php',
                type: 'POST',
                data: { color_name: colorName, form1: true },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $('#successMessage').text(data.success).removeClass('d-none');
                        $('#color_name').val('');  // Clear input field
                    } else {
                        $('#errorMessage').text(data.error).removeClass('d-none');
                    }
                },
                error: function() {
                    $('#errorMessage').text('An error occurred. Please try again.').removeClass('d-none');
                }
            });
        });
    });
</script>

