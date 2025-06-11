<?php
ob_start();
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");

// Fetch customer ID from session at the beginning
$cust_id = $_SESSION['customer']['cust_id'] ?? null;
if (!$cust_id) {
  echo "No customer logged in.";
  exit;
}

$items_per_page = 4; // Number of items per page
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Current page
$offset = ($page - 1) * $items_per_page; // Offset for pagination

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;
  $order_id = $_POST['order_id'] ?? null;
  $product_id = $_POST['product_id'] ?? null;
  $review = $_POST['review'] ?? null;
  $rating = $_POST['rating'] ?? null;

  // Add or update review
  if ($action === 'add_or_update_review') {
    try {
      // Check if a review already exists
      $stmt = $pdo->prepare("SELECT review_id FROM reviews WHERE order_id = :order_id AND product_id = :product_id AND customer_id = :customer_id");
      $stmt->execute([':order_id' => $order_id, ':product_id' => $product_id, ':customer_id' => $cust_id]);
      $existing_review = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existing_review) {
        // Update the existing review
        $stmt = $pdo->prepare("UPDATE reviews SET review = :review, rating = :rating WHERE review_id = :review_id");
        $stmt->execute([':review' => $review, ':rating' => $rating, ':review_id' => $existing_review['review_id']]);
      } else {
        // Insert new review
        $stmt = $pdo->prepare("INSERT INTO reviews (order_id, product_id, review, rating, customer_id) VALUES (:order_id, :product_id, :review, :rating, :customer_id)");
        $stmt->execute([':order_id' => $order_id, ':product_id' => $product_id, ':review' => $review, ':rating' => $rating, ':customer_id' => $cust_id]);
      }

      echo json_encode(['success' => true, 'message' => 'Review submitted or updated successfully']);
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }

  // Cancel order logic
  if ($action === 'cancel_order') {
    try {
      // Check if the order exists and if it's in a cancellable state (not shipped or already canceled)
      $stmt = $pdo->prepare("
      SELECT o.order_id, o.order_status, pay.shipping_status 
      FROM orders o
      JOIN payment pay ON o.order_id = pay.order_id
      WHERE o.order_id = :order_id AND o.customer_id = :cust_id
    ");
      $stmt->execute([':order_id' => $order_id, ':cust_id' => $cust_id]);
      $order = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$order) {
        echo json_encode(['success' => false, 'message' => 'Order not found or not associated with this account']);
        exit;
      }

      // Check if order_status exists before using it
      if (!isset($order['order_status'])) {
        echo json_encode(['success' => false, 'message' => 'Order status is missing']);
        exit;
      }

      // If the order is not shipped, update its status to canceled
      if ($order['shipping_status'] === 'pending' && $order['order_status'] !== 'canceled') {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = 'canceled' WHERE order_id = :order_id");
        $stmt->execute([':order_id' => $order_id]);
    
        // Order canceled successfully, alert and redirect
        echo "<script>
                alert('Order canceled successfully');
                window.location.href = 'customer-order.php';  // Redirect back to the order history page
              </script>";
    } else {
        // Order cannot be canceled, alert and redirect
        echo "<script>
                alert('Order cannot be canceled as it is already shipped or canceled');
                window.location.href = 'customer-order.php';  // Redirect back to the order history page
              </script>";
    }
    exit;
    
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
}
?>

<style>
  :root {
    --pink: #e84393;
    --pink: #e84393;
    --main: #d0bcb3;
    --font: #d18276;
    --button: #d6a98f;
    --bg: rgb(233, 221, 204);
  }

  html,
  body {
    height: 100%;
    /* Ensure the body takes the full height of the viewport */
    margin: 0;
    /* Remove default margins */
    display: flex;
    justify-content: center;
    /* Horizontally center the content */
    align-items: center;
    /* Vertically center the content */
  }


  .horizontal-table {
    display: flex;
    flex-wrap: wrap;
    /* Allows items to wrap on smaller screens */
    justify-content: center;
    /* Center items horizontally */
    align-items: center;
    /* Center items vertically */
    border-radius: 8px;
    margin: auto;
    /* Centers the container horiz    ontally */
    margin-top: 10rem;
    /* Adjust vertical spacing */
    width: fit-content;
    /* Adjust the width to fit content */
    margin-bottom: -5rem;
  }

  /* Style the links */
  .horizontal-table a {
    display: block;
    padding: 10px 28px;
    /* Increase padding for larger clickable areas */
    margin: 10px;
    /* Add more spacing between items */
    text-align: center;
    font-size: 1.3rem;
    /* Make text larger */
    background-color: #333;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }

  /* Hover effect */
  .horizontal-table a:hover {
    background-color: var(--button);
    transform: scale(1.05);
    /* Slightly enlarge the link */
  }

  /* Button Styling */
</style>
<?php include('navuser.php'); ?>
<link rel="stylesheet" href="../css/profileupd.css?">

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
                <a href="customer-profile-update.php">Update Profile</a>
                <a href="customer-password-update.php">Update Password</a>
                <a href="customer-order.php">Orders</a>
                <a href="customize-view.php">Custom Orders</a>
              </div>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <link rel="stylesheet" href="../css/customerorder.css?">
  <div class="containers">
    <div class="col-md-12">
      <div class="container my-4">
        <h1 class="text-center">My Orders</h1>
        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>Product Details</th>
                <th>Payment Information</th>
                <th>Paid Amount</th>
                <th>Payment Status</th>
                <th>Shipping Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              // Updated query to fetch the `price` from `order_items` table
              $stmt = $pdo->prepare("
        SELECT 
          c.cust_id, 
          c.cust_name, 
          c.cust_email, 
          p.name AS product_name, 
          oi.quantity, 
          oi.price AS paid_amount, 
          p.current_price AS unit_price, 
          pay.payment_method, 
          pay.payment_id, 
          pay.created_at AS payment_date, 
          pay.shipping_status, 
          pay.payment_status, 
          o.order_id, 
          p.p_id,
          o.order_status
        FROM 
          customer c
        JOIN orders o ON c.cust_id = o.customer_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.p_id
        JOIN payment pay ON o.order_id = pay.order_id
        WHERE c.cust_id = :cust_id
        ORDER BY pay.created_at DESC
        LIMIT :offset, :items_per_page
      ");

              $stmt->bindParam(':cust_id', $cust_id, PDO::PARAM_INT);
              $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
              $stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);

              $stmt->execute();
              $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

              if (empty($orders)) {
                echo "<tr><td colspan='8' class='text-center'>No orders found</td></tr>";
              } else {
                foreach ($orders as $index => $order) {
                  ?>
                  <tr>
                    <td>
                      <strong>Product:</strong>
                      <a href="product-details.php?p_id=<?= urlencode($order['p_id']) ?>">
                        <?= htmlspecialchars($order['product_name']) ?>
                      </a><br>
                      <strong>Quantity:</strong> <?= $order['quantity'] ?><br>
                      <strong>Unit Price:</strong> <?= $order['unit_price'] ?>
                    </td>
                    <td>
                      <strong>Payment Method:</strong> <?= $order['payment_method'] ?><br>
                      <strong>Payment Id:</strong> <?= $order['payment_id'] ?><br>
                      <strong>Date:</strong> <?= $order['payment_date'] ?>
                    </td>
                    <!-- Changed Paid Amount to display `price` from `order_items` -->
                    <td>₱<?= number_format($order['paid_amount'], 2) ?></td>
                    <td>
                      <span
                        class="badge <?= $order['payment_status'] === 'pending' ? 'bg-warning' : ($order['payment_status'] === 'paid' ? 'bg-success' : 'bg-danger') ?>">
                        <?= ucfirst($order['payment_status']) ?>
                      </span>
                    </td>
                    <td>
                      <span
                        class="badge <?= $order['shipping_status'] === 'pending' ? 'bg-warning' : ($order['shipping_status'] === 'shipped' ? 'bg-info' : 'bg-success') ?>">
                        <?= ucfirst($order['shipping_status']) ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <div style="display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 5px;">
                        <!-- Cancel Order Button -->
                        <?php if ($order['shipping_status'] === 'pending' && $order['order_status'] !== 'canceled'): ?>
                          <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal"
                            data-order-id="<?= $order['order_id'] ?>">Cancel Order</button>
                        <?php endif; ?>

                        <!-- Cancel Indicator -->
                        <?php if ($order['order_status'] === 'canceled'): ?>
                          <span class="badge bg-danger">Canceled</span>
                        <?php endif; ?>

                        <!-- Add/View Review Button -->
                        <?php
                        // Check if the order has been delivered/shipped and a review status
                        $stmt = $pdo->prepare("SELECT review_id, review, rating FROM reviews WHERE order_id = :order_id AND product_id = :product_id AND customer_id = :customer_id");
                        $stmt->execute([':order_id' => $order['order_id'], ':product_id' => $order['p_id'], ':customer_id' => $cust_id]);
                        $existing_review = $stmt->fetch(PDO::FETCH_ASSOC);

                        // Show review button when order is shipped or delivered (not just delivered)
                        if ($order['shipping_status'] === 'shipped' || $order['shipping_status'] === 'delivered'):
                            if ($existing_review):
                                // Show "Edit Review" button if review exists
                                echo "<button class='btn btn-success btn-sm' data-bs-toggle='modal' data-bs-target='#reviewModal'
                                        data-order-id='" . $order['order_id'] . "' data-product-id='" . $order['p_id'] . "' 
                                        data-existing-review='true'>Edit Review</button>";
                                echo "<a href='product-details.php?p_id=" . $order['p_id'] . "' class='btn btn-info btn-sm'>View Product</a>";
                            else:
                                // Show "Add Review" button if no review exists
                                echo "<button class='btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#reviewModal'
                                        data-order-id='" . $order['order_id'] . "' data-product-id='" . $order['p_id'] . "' 
                                        data-existing-review='false'>Add Review</button>";
                            endif;
                        elseif ($order['shipping_status'] === 'pending'):
                            echo "<small class='text-muted'>Review available after shipping</small>";
                        endif;
                        ?>
                      </div>
                    </td>
                  </tr>
                <?php }
              } ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="pagination">
          <?php
          // Calculate total pages
          $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE customer_id = :cust_id");
          $stmt->execute([':cust_id' => $cust_id]);
          $total_orders = $stmt->fetchColumn();
          $total_pages = ceil($total_orders / $items_per_page);

          // Display pagination links
          for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?page=$i' class='" . ($i === $page ? 'active' : '') . "'>$i</a> ";
          }
          ?>
        </div>
      </div>
    </div>

    <!-- Modal for Adding/Editing Review -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="reviewModalLabel">Add Review</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="reviewForm">
              <div class="mb-3">
                <label for="review" class="form-label">Your Review</label>
                <textarea id="review" class="form-control" rows="4" placeholder="Write your review here..." required></textarea>
              </div>
              <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select id="rating" class="form-select" required>
                  <option value="">Select Rating</option>
                  <option value="1">⭐ 1 Star</option>
                  <option value="2">⭐⭐ 2 Stars</option>
                  <option value="3">⭐⭐⭐ 3 Stars</option>
                  <option value="4">⭐⭐⭐⭐ 4 Stars</option>
                  <option value="5">⭐⭐⭐⭐⭐ 5 Stars</option>
                </select>
              </div>
              <input type="hidden" id="order-id">
              <input type="hidden" id="product-id">
              <button type="submit" class="btn btn-primary w-100">Submit Review</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for cancel confirmation -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="cancelModalLabel">Cancel Order</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to cancel this order? This action cannot be undone. <br>
            Message us on Facebook if you want a refund.
          </div>
          <div class="modal-footer">
            <form method="POST" action="customer-order.php">
              <input type="hidden" name="action" value="cancel_order">
              <input type="hidden" id="cancelOrderId" name="order_id">
              <button type="submit" class="btn btn-danger">Cancel Order</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    // Handle cancel order modal
    const cancelButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#cancelModal"]');
    cancelButtons.forEach(button => {
      button.addEventListener('click', function () {
        const orderId = this.getAttribute('data-order-id');
        document.getElementById('cancelOrderId').value = orderId;
      });
    });

    // Handle review modal
    const reviewModal = document.getElementById('reviewModal');
    const reviewButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#reviewModal"]');
    
    reviewButtons.forEach(button => {
      button.addEventListener('click', function () {
        const orderId = this.getAttribute('data-order-id');
        const productId = this.getAttribute('data-product-id');
        const hasExistingReview = this.getAttribute('data-existing-review') === 'true';
        
        document.getElementById('order-id').value = orderId;
        document.getElementById('product-id').value = productId;
        
        // Update modal title based on whether it's adding or editing
        const modalTitle = document.getElementById('reviewModalLabel');
        const submitButton = document.querySelector('#reviewForm button[type="submit"]');
        
        if (hasExistingReview) {
          modalTitle.textContent = 'Edit Review';
          submitButton.textContent = 'Update Review';
          
          // Fetch existing review data
          fetch(`get_review.php?order_id=${orderId}&product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
              document.getElementById('review').value = data.review || '';
              document.getElementById('rating').value = data.rating || '';
            })
            .catch(error => {
              console.error('Error fetching review:', error);
              // Clear the form if there's an error
              document.getElementById('review').value = '';
              document.getElementById('rating').value = '';
            });
        } else {
          modalTitle.textContent = 'Add Review';
          submitButton.textContent = 'Submit Review';
          // Clear the form for new review
          document.getElementById('review').value = '';
          document.getElementById('rating').value = '';
        }
      });
    });

    // Handle review form submission
    document.getElementById('reviewForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const orderId = document.getElementById('order-id').value;
      const productId = document.getElementById('product-id').value;
      const review = document.getElementById('review').value;
      const rating = document.getElementById('rating').value;

      if (!review.trim() || !rating) {
        alert('Please fill in both review and rating fields.');
        return;
      }

      fetch('', {
        method: 'POST',
        body: new URLSearchParams({
          action: 'add_or_update_review',
          order_id: orderId,
          product_id: productId,
          review: review,
          rating: rating
        }),
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Review submitted successfully!');
            window.location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while submitting your review.');
        });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    @import url(https://db.onlinewebfonts.com/c/90ac3b18aaef9f2db3ac8e062c7a033b?family=NudMotoya+Maru+W55+W5);

    /* General table container styling */
    .table-container {
      max-height: none;
      border: 1px solid #ddd;
      border-radius: 5px;
      background-color: #f9f9f9;
      margin-top: 20px;
      padding: 10px;
      font-family: "NudMotoya Maru W55 W5";
      font-size: 12px;
    }

    .table thead th {
      background-color: #e84393;
      color: white;
      text-align: center;
      font-weight: bold;
    }

    .table tbody tr:hover {
      background-color: #f1f1f1;
    }

    /* Button styling improvements */
    .btn-sm {
      font-size: 11px;
      padding: 4px 8px;
      margin: 2px;
      border-radius: 3px;
      min-width: 80px;
    }

    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
    }

    .btn-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }

    .btn-success {
      background-color: #28a745;
      border-color: #28a745;
    }

    .btn-success:hover {
      background-color: #218838;
      border-color: #218838;
    }

    .btn-info {
      background-color: #17a2b8;
      border-color: #17a2b8;
    }

    .btn-info:hover {
      background-color: #138496;
      border-color: #138496;
    }

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
    }

    .btn-danger:hover {
      background-color: #c82333;
      border-color: #c82333;
    }

    .btn-secondary {
      background-color: #6c757d;
      border-color: #6c757d;
    }

    .text-muted {
      font-size: 10px;
      font-style: italic;
    }

    /* Modal improvements */
    .modal-header {
      color: white;
    }

    .modal .btn-primary {
      background-color: #28a745;
      border-color: #28a745;
      font-weight: bold;
    }

    .modal .btn-primary:hover {
      background-color: #218838;
      border-color: #218838;
    }

    .modal textarea,
    .modal select {
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    .form-label {
      font-weight: bold;
      color: #333;
    }

    /* Pagination styling */
    .pagination {
      margin-top: 20px;
      text-align: center;
    }

    .pagination a {
      padding: 6px 12px;
      text-decoration: none;
      color: #007bff;
      margin: 0 5px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 12px;
    }

    .pagination a.active {
      background-color: #007bff;
      color: white;
    }

    .pagination a:hover {
      background-color: #f1f1f1;
    }

    /* Action column styling */
    td:last-child {
      min-width: 120px;
    }

    /* Badge styling */
    .badge {
      font-size: 10px;
      padding: 4px 8px;
    }
  </style>