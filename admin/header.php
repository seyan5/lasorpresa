<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Sorpresa Admin</title>
    <link rel="stylesheet" href="../css/style.css">

    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Include jQuery (Ensure jQuery is loaded before Bootstrap JS) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>

<script>
$(document).ready(function () {
    // Initialize Select2 Elements
    $(".select2").select2();

    // Handle Top Level Category change
    $('#tcat_id').change(function() {
        var tcatId = $(this).val(); // Get selected top level category ID

        // Clear the Mid Level Category dropdown
        $('#mcat_id').html('<option value="">Select Mid Level Category</option>');

        // If a Top Level Category is selected, make an AJAX call to fetch Mid Level Categories
        if (tcatId) {
            $.ajax({
                url: 'fetch_mid_categories.php',  // URL to fetch mid categories
                type: 'POST',
                data: { tcat_id: tcatId },  // Send top category ID
                success: function(response) {
                    $('#mcat_id').html(response); // Populate Mid Level Category dropdown with the response
                    $(".select2").select2(); // Reinitialize Select2 for the new options
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching mid level categories: " + error);
                    alert('Failed to load Mid Level Categories.');
                }
            });
        }
    });

    // Modal for delete confirmation
    $('#confirm-delete').on('show.bs.modal', function (e) {
        var deleteUrl = $(e.relatedTarget).data('href'); // Get href of the clicked button
        $(this).find('.btn-confirm-delete').attr('href', deleteUrl); // Set it in the modal's delete button
    });

    // Confirm Delete action with AJAX for smoother deletion
    $('.btn-confirm-delete').on('click', function () {
        var href = $(this).attr('href');
        $.ajax({
            url: href,  // URL to delete the item
            type: 'GET',
            success: function (response) {
                // On success, close the modal and reload the page to reflect changes
                $('#confirm-delete').modal('hide');
                location.reload();
            },
            error: function (xhr, status, error) {
                console.error("Error in deletion: " + error);
                alert('Error in deletion. Please try again.');
            }
        });
    });

    // Handle the dynamic addition and deletion of product photos
    $("#btnAddNew").click(function () {
        var rowNumber = $("#ProductTable tbody tr").length;
        var addLink = "<div class=\"upload-btn" + rowNumber + "\"><input type=\"file\" name=\"photo[]\" style=\"margin-bottom:5px;\"></div>";
        var deleteRow = "<a href=\"javascript:void()\" class=\"Delete btn btn-danger btn-xs\">X</a>";

        var trNew = "<tr><td>" + addLink + "</td><td style=\"width:28px;\">" + deleteRow + "</td></tr>";

        $("#ProductTable tbody").append(trNew); // Add new row to the table

        // Reinitialize Select2 on the newly added elements (if any)
        $(".select2").select2();
    });

    // Remove a photo row when clicked
    $('#ProductTable').delegate('a.Delete', 'click', function () {
        $(this).parent().parent().fadeOut('slow').remove();
        return false;
    });

});
</script>

<body>
    <form class="form-horizontal" action="" method="post">
        <div class="box box-info">
            <div class="box-body">
                <!-- Top Level Category Selection -->
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Top Level Category Name <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="tcat_id" id="tcat_id" class="form-control select2 top-cat">
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

                <!-- Mid Level Category Selection (Initially empty) -->
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Mid Level Category Name <span>*</span></label>
                    <div class="col-sm-4">
                        <select name="mcat_id" id="mcat_id" class="form-control select2 mid-cat">
                            <option value="">Select Mid Level Category</option>
                        </select>
                    </div>
                </div>

                <!-- End Level Category -->
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">End Level Category Name <span>*</span></label>
                    <div class="col-sm-4">
                        <input type="text" class="form-control" name="ecat_name">
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label"></label>
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success pull-left" name="form1">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>
