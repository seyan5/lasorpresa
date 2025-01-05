<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");
$csrf = new CSRF_Protect();
$error_message = '';
$success_message = '';
$error_message1 = '';
$success_message1 = '';

// Check if the user is logged in or not
if(!isset($_SESSION['user'])) {
    header('location: ../login.php');
    exit;
}
?>

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

    // Modal for delete confirmation
    $('#confirm-delete').on('show.bs.modal', function (e) {
        var deleteUrl = $(e.relatedTarget).data('href'); // Get href of the clicked button
        $(this).find('.btn-confirm-delete').attr('href', deleteUrl); // Set it in the modal's delete button
    });

    // Confirm Delete action
    $('.btn-confirm-delete').on('click', function () {
        var href = $(this).attr('href');
        window.location.href = href; // Redirect to delete URL
    });

    // Handle the dynamic addition and deletion of product photos
    $("#btnAddNew").click(function () {
        var rowNumber = $("#ProductTable tbody tr").length;
        var addLink = "<div class=\"upload-btn" + rowNumber + "\"><input type=\"file\" name=\"photo[]\" style=\"margin-bottom:5px;\"></div>";
        var deleteRow = "<a href=\"javascript:void()\" class=\"Delete btn btn-danger btn-xs\">X</a>";

        var trNew = "<tr><td>" + addLink + "</td><td style=\"width:28px;\">" + deleteRow + "</td></tr>";

        $("#ProductTable tbody").append(trNew); // Add new row to the table
    });

    // Remove a photo row when clicked
    $('#ProductTable').delegate('a.Delete', 'click', function () {
        $(this).parent().parent().fadeOut('slow').remove();
        return false;
    });

    $(document).ready(function() {
    // When a top-level category is selected
    $('#tcat_id').change(function() {
        var tcat_id = $(this).val(); // Get the selected top-level category ID
        
        if (tcat_id) {
            // If a top-level category is selected, fetch the related mid-level categories
            $.ajax({
                url: 'fetch-midcategories.php',  // Adjust the path if necessary
                type: 'POST',
                data: { tcat_id: tcat_id },  // Pass the selected top-level category ID
                success: function(data) {
                    // Populate the mid-level categories dropdown
                    $('.mid-cat').html(data);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + xhr.responseText);
                    alert('Error loading mid-level categories: ' + error);
                }
            });
        } else {
            // If no top-level category is selected, reset the mid-level categories dropdown
            $('.mid-cat').html('<option value="">Select Mid Level Category</option>');
        }
    });
});
});
</script>