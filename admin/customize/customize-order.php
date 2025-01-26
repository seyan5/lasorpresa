<?php
ob_start();
session_start();
include("../inc/config.php");
include("../inc/functions.php");
include("../inc/CSRF_Protect.php");

// Define how many results per page
$results_per_page = 8;

// Find out the total number of order items
$total_query = $pdo->prepare("SELECT COUNT(*) FROM custom_orderitems");
$total_query->execute();
$total_orderitems = $total_query->fetchColumn();

// Calculate total pages
$total_pages = ceil($total_orderitems / $results_per_page);

// Get the current page number from the query string, default is 1
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;

// Prevent out-of-range page numbers
$current_page = max(1, min($current_page, $total_pages));

// Determine the starting limit number for the SQL query
$starting_limit = ($current_page - 1) * $results_per_page;

// Fetch data with limit and offset
$query = $pdo->prepare(
    "SELECT 
        coi.orderitem_id,
        co.order_id,
        co.customer_name,
        co.customer_email,
        co.shipping_address,
        co.order_date,
        coi.flower_details AS product_details,
        CONCAT(coi.container_type, ' (₱', coi.container_price, ')') AS container_type,
        coi.container_color AS container_color,
        coi.remarks AS remarks,
        cp.payment_method,
        cp.amount_paid,
        cp.payment_status,
        cp.shipping_status,
        ci.expected_image,
        cf.final_image
    FROM custom_orderitems coi
    LEFT JOIN custom_order co ON coi.order_id = co.order_id
    LEFT JOIN custom_payment cp ON co.order_id = cp.order_id
    LEFT JOIN custom_images ci ON coi.orderitem_id = ci.orderitem_id
    LEFT JOIN custom_finalimages cf ON coi.orderitem_id = cf.orderitem_id
    ORDER BY coi.orderitem_id DESC
    LIMIT :starting_limit, :results_per_page"
);

$query->bindValue(':starting_limit', $starting_limit, PDO::PARAM_INT);
$query->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
$query->execute();
$orderitems = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Order Dashboard</title>
</head>

<body>
    <div class="container">
        <h3>Custom Order Dashboard</h3>
        <div class="d-flex justify-content-start mb-3">
            <a href="../dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        <table class="custom-table">
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
                <?php foreach ($orderitems as $index => $orderitem): ?>
                    <tr>
                        <td><?= $starting_limit + $index + 1; ?></td>
                        <td>
                            <strong>Order Item ID:</strong> <?= htmlspecialchars($orderitem['orderitem_id']); ?><br>
                            <strong>Order ID:</strong> <?= htmlspecialchars($orderitem['order_id']); ?><br>
                            <strong>Name:</strong> <?= htmlspecialchars($orderitem['customer_name']); ?><br>
                            <strong>Email:</strong> <?= htmlspecialchars($orderitem['customer_email']); ?>
                        </td>
                        <td><?= htmlspecialchars($orderitem['product_details'] ?? 'N/A'); ?></td>
                        <td>
                            <?= htmlspecialchars($orderitem['container_type'] ?? 'N/A'); ?><br>
                            <strong>Color:</strong> <?= htmlspecialchars($orderitem['container_color'] ?? 'N/A'); ?>
                        </td>
                        <td><?= htmlspecialchars($orderitem['remarks'] ?? 'N/A'); ?></td>
                        <td>
                            <strong>Method:</strong> <?= htmlspecialchars($orderitem['payment_method'] ?? 'N/A'); ?><br>
                            <strong>Date:</strong> <?= htmlspecialchars($orderitem['order_date'] ?? 'N/A'); ?>
                        </td>
                        <td>₱<?= number_format($orderitem['amount_paid'] ?? 0, 2); ?></td>
                        <td>
                            <select class="form-control change-status"
                                data-orderitem-id="<?= $orderitem['orderitem_id']; ?>" data-field="payment_status">
                                <option value="Pending" <?= $orderitem['payment_status'] == 'Pending' ? 'selected' : ''; ?>>
                                    Pending</option>
                                <option value="Paid" <?= $orderitem['payment_status'] == 'Paid' ? 'selected' : ''; ?>>Paid
                                </option>
                                <option value="Failed" <?= $orderitem['payment_status'] == 'Failed' ? 'selected' : ''; ?>>
                                    Failed</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control change-status"
                                data-orderitem-id="<?= $orderitem['orderitem_id']; ?>" data-field="shipping_status">
                                <option value="Pending" <?= $orderitem['shipping_status'] == 'Pending' ? 'selected' : ''; ?>>
                                    Pending</option>
                                <option value="Shipped" <?= $orderitem['shipping_status'] == 'Shipped' ? 'selected' : ''; ?>>
                                    Shipped</option>
                                <option value="Delivered" <?= $orderitem['shipping_status'] == 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="Cancelled" <?= $orderitem['shipping_status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                <option value="ReadyForPickup" <?= $orderitem['shipping_status'] == 'ReadyForPickup' ? 'selected' : ''; ?>>Ready For Pickup</option>
                            </select>
                        </td>

                        <td>
                            <button class="btn btn-info btn-sm"
                                onclick="viewImages(<?= $orderitem['orderitem_id']; ?>, <?= $orderitem['order_id']; ?>, '<?= htmlspecialchars($orderitem['expected_image'] ?? ''); ?>', '<?= htmlspecialchars($orderitem['final_image'] ?? ''); ?>')">
                                View Pictures
                            </button>

                            <button class="btn btn-danger btn-sm" onclick="deleteOrder(<?= $orderitem['order_id']; ?>)">
                                Delete Order Item
                            </button>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1; ?>" class="btn btn-secondary">Previous</a>
            <?php endif; ?>

            <?php for ($page = 1; $page <= $total_pages; $page++): ?>
                <a href="?page=<?= $page; ?>" class="btn <?= $page == $current_page ? 'active' : ''; ?>">
                    <?= $page; ?>
                </a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1; ?>" class="btn btn-secondary">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="imageModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="modal-header">
                <h4>Order Images</h4>
            </div>
            <h5>Expected Images</h5>
            <div id="expectedImagesContainer" class="modal-images"></div>

            <h5>Final Images</h5>
            <div id="finalImagesContainer" class="modal-images"></div>

            <form id="addFinalImageForm" enctype="multipart/form-data">
                <input type="hidden" id="orderitemIdInput" name="orderitem_id">
                <input type="hidden" id="orderIdInput" name="order_id">
                <input type="file" name="final_image" required>
                <button type="submit" class="btn btn-info">Add Final Image</button>
            </form>


            <div id="finalImageStatus" style="margin-top: 10px; color: green;"></div>
        </div>
    </div>

    </div>



    <script>
        function viewImages(orderitemId, orderId, expectedImages = null, finalImages = null) {
            const modal = document.getElementById("imageModal");
            const expectedContainer = document.getElementById("expectedImagesContainer");
            const finalContainer = document.getElementById("finalImagesContainer");
            const orderitemIdInput = document.getElementById("orderitemIdInput");
            const orderIdInput = document.getElementById("orderIdInput");

            // Set the orderitem_id and order_id for the add-final-image form
            orderitemIdInput.value = orderitemId;
            orderIdInput.value = orderId;

            // Fetch updated data from the server
            fetch(`get-order-images.php?orderitem_id=${orderitemId}`)
                .then((response) => response.json())
                .then((data) => {
                    // Clear existing images
                    expectedContainer.innerHTML = "";
                    finalContainer.innerHTML = "";

                    // Populate Expected Images
                    if (data.expected_images && data.expected_images.length) {
                        data.expected_images.forEach((img) => {
                            const imgElement = document.createElement("img");
                            imgElement.src = `../../users/uploads/${img.trim()}`;
                            imgElement.alt = "Expected Image";
                            expectedContainer.appendChild(imgElement);
                        });
                    } else {
                        expectedContainer.textContent = "No expected images available.";
                    }

                    // Populate Final Images with Remove Button
                    if (data.final_images && data.final_images.length) {
                        data.final_images.forEach((img) => {
                            const imgWrapper = document.createElement("div");
                            imgWrapper.style.display = "inline-block";
                            imgWrapper.style.margin = "10px";

                            const imgElement = document.createElement("img");
                            imgElement.src = `final_image_uploads/${img.trim()}`;
                            imgElement.alt = "Final Image";
                            imgElement.style.display = "block";
                            imgElement.style.marginBottom = "5px";

                            const removeButton = document.createElement("button");
                            removeButton.textContent = "Remove";
                            removeButton.classList.add("btn", "btn-danger", "btn-sm");
                            removeButton.onclick = function () {
                                removeFinalImage(orderitemId, img.trim(), imgWrapper);
                            };

                            imgWrapper.appendChild(imgElement);
                            imgWrapper.appendChild(removeButton);
                            finalContainer.appendChild(imgWrapper);
                        });
                    } else {
                        finalContainer.textContent = "No final images available.";
                    }

                    // Display the modal
                    modal.style.display = "block";
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Failed to load images.");
                });
        }


        function removeFinalImage(orderItemId, imageName, imgWrapper) {
            if (confirm("Are you sure you want to remove this image?")) {
                const data = {
                    orderitem_id: orderItemId,
                    image_name: imageName,
                };

                console.log("Data being sent to server:", data); // Debugging

                fetch("remove-final-image.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        console.log("Response from server:", data); // Debugging
                        if (data.success) {
                            imgWrapper.remove(); // Remove the image element from the modal
                            alert(data.message);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("Failed to remove the image.");
                    });
            }
        }


        document.getElementById("addFinalImageForm").addEventListener("submit", function (e) {
            e.preventDefault(); // Prevent default form submission

            const form = e.target;
            const formData = new FormData(form);
            const orderitemId = document.getElementById("orderitemIdInput").value;
            const orderId = document.getElementById("orderIdInput").value;

            fetch("add-final-image.php", {
                method: "POST",
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);

                        // Refresh final images in the modal
                        viewImages(orderitemId, orderId); // Reload modal content
                    } else {
                        alert(data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Failed to upload the image.");
                });
        });


        function closeModal() {
            const modal = document.getElementById("imageModal");
            modal.style.display = "none"; // Hide modal
            document.getElementById("expectedImagesContainer").innerHTML = ""; // Clear containers
            document.getElementById("finalImagesContainer").innerHTML = "";
            document.getElementById("finalImageStatus").textContent = ""; // Clear status message
        }

        document.querySelectorAll(".change-status").forEach((dropdown) => {
            dropdown.addEventListener("change", function () {
                const orderId = this.dataset.orderId; // Order ID
                const field = this.dataset.field; // Field to update (payment_status or shipping_status)
                const value = this.value; // New value selected

                // Send the POST request to customize-order-change-status.php
                fetch("customize-order-change-status.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded", // Form URL encoding
                    },
                    body: new URLSearchParams({
                        order_id: orderId,
                        field: field,
                        value: value,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert(data.message); // Success message
                        } else {
                            alert(data.message); // Error message
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("Failed to update the status.");
                    });
            });
        });

        function deleteOrder(orderId) {
            if (confirm("Are you sure you want to delete this order? This action cannot be undone.")) {
                fetch("customize-order-delete.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: new URLSearchParams({
                        order_id: orderId,
                    }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            alert(data.message); // Show success message
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert(data.message); // Show error message
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("Failed to delete the order.");
                    });
            }
        }



    </script>
    <style>
        /* Keep the existing CSS you provided */
        @import url(https://db.onlinewebfonts.com/c/90ac3b18aaef9f2db3ac8e062c7a033b?family=NudMotoya+Maru+W55+W5);

        :root {
            --pink: #e84393;
        }

        body {
            font-family: "NudMotoya Maru W55 W5", sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 15px;
            /* Reduced margin */
            line-height: 1.4;
            /* Slightly reduced line height */
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
            /* Reduced margin */
            color: #444;
            font-size: 2.5rem;
            /* Slightly smaller font size */
        }

        .container {
            width: 85%;
            /* Reduced width slightly */
            margin: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            /* Reduced margin */
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* Reduced shadow */
            font-family: "NudMotoya Maru W55 W5", sans-serif;
        }

        .custom-table th,
        .custom-table td {
            padding: 12px;
            /* Reduced padding */
            text-align: center;
        }

        .custom-table th {
            background-color: var(--pink);
            color: #fff;
            text-transform: uppercase;
            font-size: 0.85rem;
            /* Slightly smaller font size */
            letter-spacing: 0.8px;
            /* Slightly reduced letter spacing */
        }

        .custom-table tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .custom-table tr:nth-child(even) {
            background-color: #fff;
        }

        .custom-table tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            padding: 6px 12px;
            /* Reduced padding */
            font-size: 0.8rem;
            /* Smaller font size */
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-secondary {
            background-color: #555;
            color: #fff;
        }

        .btn-secondary:hover {
            background-color: #444;
        }

        .btn-info {
            background-color: #007bff;
            color: #fff;
        }

        .btn-info:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #a71d2a;
        }

        .form-control {
            padding: 4px 8px;
            /* Reduced padding */
            font-size: 0.85rem;
            /* Slightly smaller font size */
            font-family: "NudMotoya Maru W55 W5", sans-serif;
        }

        .form-control select {
            width: 100%;
        }

        /* Pagination Styles */
        .pagination {
            text-align: center;
            margin-top: 15px;
            /* Reduced margin */
        }

        .pagination a {
            margin: 0 4px;
            /* Reduced margin */
            padding: 6px 10px;
            /* Reduced padding */
            background-color: #ddd;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #ccc;
        }

        .pagination .active {
            background-color: #007bff;
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #ddd;
            width: 60%;
            border-radius: 8px;
            text-align: center;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
        }

        img {
            max-width: 100px;
            margin: 10px;
        }

        .modal-images {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        .modal-header h4 {
            margin: 0;
        }
    </style>

</body>

</html>