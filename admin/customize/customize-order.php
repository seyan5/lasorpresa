<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

// Fetch data from custom_order, custom_orderitems, custom_payment, and custom_images tables
$query = $pdo->prepare(
    "SELECT 
        co.order_id,
        co.customer_name,
        co.customer_email,
        co.shipping_address,
        co.order_date,
        GROUP_CONCAT(DISTINCT CONCAT(coi.flower_type, ' (', coi.num_flowers, ')') SEPARATOR '<br>') AS product_details,
        GROUP_CONCAT(DISTINCT coi.container_type SEPARATOR ', ') AS container_types,
        GROUP_CONCAT(DISTINCT coi.container_color SEPARATOR ', ') AS container_colors,
        GROUP_CONCAT(DISTINCT coi.remarks SEPARATOR '<br>') AS remarks,
        cp.payment_method,
        cp.amount_paid,
        cp.payment_status,
        cp.shipping_status,
        GROUP_CONCAT(DISTINCT ci.expected_image SEPARATOR ', ') AS expected_images,
        GROUP_CONCAT(DISTINCT cf.final_image SEPARATOR ', ') AS final_images
    FROM custom_order co
    LEFT JOIN custom_orderitems coi ON co.order_id = coi.order_id
    LEFT JOIN custom_payment cp ON co.order_id = cp.order_id
    LEFT JOIN custom_images ci ON co.order_id = ci.order_id
    LEFT JOIN custom_finalimages cf ON co.order_id = cf.order_id
    GROUP BY co.order_id
    ORDER BY co.order_id DESC"
);

$query->execute();
$orders = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h3 class="text-center">Order Dashboard</h3>
        <div class="d-flex justify-content-start mb-3">
            <a href="../dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Product Details</th>
                    <th>Container</th>
                    <th>Remarks</th>
                    <th>Payment Info</th>
                    <th>Paid Amount</th>
                    <th>Payment Status</th>
                    <th>Shipping Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $index => $order): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td>
                            <strong>Id:</strong> <?= htmlspecialchars($order['order_id']); ?><br>
                            <strong>Name:</strong> <?= htmlspecialchars($order['customer_name']); ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($order['customer_email']); ?>
                        </td>
                        <td><?= $order['product_details'] ?? 'N/A'; ?></td>
                        <td>
                            <strong>Type:</strong> <?= htmlspecialchars($order['container_types'] ?? 'N/A'); ?><br>
                            <strong>Color:</strong> <?= htmlspecialchars($order['container_colors'] ?? 'N/A'); ?>
                        </td>
                        <td><?= htmlspecialchars($order['remarks'] ?? 'N/A'); ?></td>
                        <td>
                            <strong>Method:</strong> <?= htmlspecialchars($order['payment_method'] ?? 'N/A'); ?><br>
                            <strong>Date:</strong> <?= htmlspecialchars($order['order_date'] ?? 'N/A'); ?>
                        </td>
                        <td>₱<?= number_format($order['amount_paid'] ?? 0, 2); ?></td>
                        <td><?= htmlspecialchars($order['payment_status'] ?? 'Pending'); ?></td>
                        <td><?= htmlspecialchars($order['shipping_status'] ?? 'Pending'); ?></td>
                        <td>
                            <button class="btn btn-info btn-sm view-images" data-order-id="<?= $order['order_id']; ?>"
                                data-expected-images="<?= htmlspecialchars($order['expected_images']); ?>"
                                data-final-images="<?= htmlspecialchars($order['final_images']); ?>">
                                View Pictures
                            </button>
                            <button class="btn btn-danger btn-sm delete-order" data-order-id="<?= $order['order_id']; ?>">
                                Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for Viewing and Adding Images -->
    <div class="modal fade" id="viewImagesModal" tabindex="-1" aria-labelledby="viewImagesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewImagesModalLabel">Order Images</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <p><strong>Expected Images:</strong></p>
                        <div id="expectedImagesContainer"></div>
                    </div>
                    <div class="text-center mt-3">
                        <p><strong>Final Images:</strong></p>
                        <div id="finalImagesContainer"></div>
                    </div>
                    <hr>
                    <h6>Add Final Image:</h6>
                    <form id="addFinalImageForm" enctype="multipart/form-data">
                        <input type="hidden" name="order_id" id="orderIdForImage">
                        <div class="form-group">
                            <label for="finalImageInput">Upload Final Image</label>
                            <input type="file" class="form-control-file" id="finalImageInput" name="final_image"
                                accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </form>
                    <hr>
                    <h6>Remove Final Image:</h6>
                    <button id="removeFinalImageBtn" class="btn btn-danger btn-sm" style="display: none;">
                        <i class="fa fa-times"></i> Remove Final Image
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // View Images
            $('.view-images').on('click', function () {
                const expectedImages = $(this).data('expected-images');
                const finalImages = $(this).data('final-images');
                const orderId = $(this).data('order-id');

                $('#orderIdForImage').val(orderId);
                $('#expectedImagesContainer').html('');
                $('#finalImagesContainer').html('');
                $('#removeFinalImageBtn').hide(); // Hide remove button initially

                if (expectedImages) {
                    const expectedImagesArray = expectedImages.split(', ');
                    expectedImagesArray.forEach(image => {
                        $('#expectedImagesContainer').append(`<img src="../../users/uploads/${image}" class="img-fluid mb-2" alt="Expected Image" style="max-width: 100%;">`);
                    });
                } else {
                    $('#expectedImagesContainer').html('<p>No expected images available.</p>');
                }

                // Display final images and show remove button if images exist
                if (finalImages) {
                    const finalImagesArray = finalImages.split(', ');
                    finalImagesArray.forEach(image => {
                        $('#finalImagesContainer').append(`<img src="final_image_uploads/${image}" class="img-fluid mb-2" alt="Final Image" style="max-width: 100%;">`);
                    });
                    $('#removeFinalImageBtn').show(); // Show remove button if there is a final image
                } else {
                    $('#finalImagesContainer').html('<p>No final images available.</p>');
                }

                $('#viewImagesModal').modal('show');
            });

            // Upload Final Image
            $('#addFinalImageForm').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                $.ajax({
                    url: 'add-final-image.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        try {
                            const data = JSON.parse(response);  // Parse the response to handle it properly
                            if (data.success) {
                                alert(data.message);  // Display success message
                            } else {
                                alert(data.message);  // Display failure message
                            }
                        } catch (error) {
                            alert('An error occurred while processing the image.'); // Handle any parsing errors
                        }

                        $('#viewImagesModal').modal('hide');
                        location.reload(); // Reload the page to reflect changes
                    },
                    error: function () {
                        alert('Failed to upload the image. Please try again.');
                    }
                });
            });



            // Remove Final Image
            $('#removeFinalImageBtn').on('click', function () {
                const orderId = $('#orderIdForImage').val();

                if (confirm('Are you sure you want to remove this final image?')) {
                    $.post('remove-final-image.php', { order_id: orderId }, function (response) {
                        if (response.success) {
                            alert(response.message);
                            $('#viewImagesModal').modal('hide');
                            location.reload();
                        } else {
                            alert(response.message);
                        }
                    }, 'json');
                }
            });

            // Delete Order
            $('.delete-order').on('click', function () {
                const orderId = $(this).data('order-id');

                if (confirm('Are you sure you want to delete this order?')) {
                    $.post('customize-order-delete.php', { order_id: orderId }, function (response) {
                        alert(response.message);
                        location.reload();
                    }, 'json');
                }
            });
        });


    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>