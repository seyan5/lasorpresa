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

    <!-- Include jQuery -->
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
        var trNew = "";              
        var addLink = "<div class=\"upload-btn" + rowNumber + "\"><input type=\"file\" name=\"photo[]\"  style=\"margin-bottom:5px;\"></div>";
        var deleteRow = "<a href=\"javascript:void()\" class=\"Delete btn btn-danger btn-xs\">X</a>";
        trNew = trNew + "<tr> ";
        trNew += "<td>" + addLink + "</td>";
        trNew += "<td style=\"width:28px;\">" + deleteRow + "</td>";
        trNew = trNew + " </tr>";
        $("#ProductTable tbody").append(trNew);
    });

    // Remove a photo row when clicked
    $('#ProductTable').delegate('a.Delete', 'click', function () {
        $(this).parent().parent().fadeOut('slow').remove();
        return false;
    });
});
</script>