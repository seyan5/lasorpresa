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
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<script>
    $(document).ready(function () {
        // Trigger when the modal is about to be shown
        $('#confirm-delete').on('show.bs.modal', function (e) {
            // Get the data-href attribute from the clicked link
            var href = $(e.relatedTarget).data('href');
            // Update the href attribute of the confirmation button
            $(this).find('.btn-ok').attr('href', href);
        });
    });
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#tcat_id').change(function() {
        let tcat_id = $(this).val();
        if (tcat_id) {
            $.ajax({
                url: 'settings/fetch-category.php', // The PHP script for fetching mid-level categories
                type: 'POST',
                data: { tcat_id: tcat_id },
                success: function(data) {
                    $('.mid-cat').html(data); // Populate mid-level category dropdown
                },
                error: function() {
                    alert('Error loading mid-level categories');
                }
            });
        } else {
            $('.mid-cat').html('<option value="">Select Mid Level Category</option>');
        }
    });
});
</script>