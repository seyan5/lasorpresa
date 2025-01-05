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
        $('#confirm-delete').on('show.bs.modal', function(e) {
	      $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
	    });
		
		$('#confirm-approve').on('show.bs.modal', function(e) {
	      $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
	    });
    });

    function confirmDelete()
	    {
	        return confirm("Are you sure want to delete this data?");
	    }
	    function confirmActive()
	    {
	        return confirm("Are you sure want to Active?");
	    }
	    function confirmInactive()
	    {
	        return confirm("Are you sure want to Inactive?");
	    }

</script>


<script>
$(document).ready(function() {
    // Fetch mid-level categories when a top-level category is selected
    $('#tcat_id').change(function() {
        var tcat_id = $(this).val();
        console.log('Selected Top Level Category ID:', tcat_id);  // Log tcat_id
        
        if (tcat_id) {
            $.ajax({
                url: '/lasorpresa/admin/settings/fetch-category.php',  // Updated to the correct path
                type: 'POST',
                data: { tcat_id: tcat_id },
                success: function(data) {
                    console.log('AJAX Response for Mid-Level Categories:', data);  // Log the server response
                    $('.mid-cat').html(data);  // Update the mid-level category dropdown
                    $('.end-cat').html('<option value="">Select End Level Category</option>');  // Reset the end-level category dropdown
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + xhr.responseText);
                    alert('Error loading mid-level categories: ' + error);
                }
            });
        } else {
            $('.mid-cat').html('<option value="">Select Mid Level Category</option>');
            $('.end-cat').html('<option value="">Select End Level Category</option>');  // Reset end-level category dropdown
        }
    });

    // Fetch end-level categories when a mid-level category is selected
    $('.mid-cat').change(function() {
        var mcat_id = $(this).val();
        console.log('Selected Mid Level Category ID:', mcat_id);  // Log mcat_id
        
        if (mcat_id) {
            $.ajax({
                url: '/lasorpresa/admin/settings/fetch-category.php',  // Updated to the correct path
                type: 'POST',
                data: { mcat_id: mcat_id },
                success: function(data) {
                    console.log('AJAX Response for End-Level Categories:', data);  // Log the server response
                    $('.end-cat').html(data);  // Update the end-level category dropdown
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: " + xhr.responseText);
                    alert('Error loading end-level categories: ' + error);
                }
            });
        } else {
            $('.end-cat').html('<option value="">Select End Level Category</option>');
        }
    });
});
</script>

<script type="text/javascript">

        $(document).ready(function () {

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

		    $('#ProductTable').delegate('a.Delete', 'click', function () {
		        $(this).parent().parent().fadeOut('slow').remove();
		        return false;
		    });

        });
</script>