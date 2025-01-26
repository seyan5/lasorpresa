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
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $items_per_page; // Offset for pagination

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? null;
  $order_id = $_POST['order_id'] ?? null;
  $product_id = $_POST['product_id'] ?? null;
  $review = $_POST['review'] ?? null;
  $rating = $_POST['rating'] ?? null;

  if ($action === 'add_or_update_review') {
    try {
      // Check if a review already exists
      $stmt = $pdo->prepare("SELECT review_id FROM reviews WHERE order_id = :order_id AND product_id = :product_id AND customer_id = :customer_id");
      $stmt->execute([ ':order_id' => $order_id, ':product_id' => $product_id, ':customer_id' => $cust_id ]);
      $existing_review = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existing_review) {
        // Update the existing review
        $stmt = $pdo->prepare("UPDATE reviews SET review = :review, rating = :rating WHERE review_id = :review_id");
        $stmt->execute([ ':review' => $review, ':rating' => $rating, ':review_id' => $existing_review['review_id'] ]);
      } else {
        // Insert new review
        $stmt = $pdo->prepare("INSERT INTO reviews (order_id, product_id, review, rating, customer_id) VALUES (:order_id, :product_id, :review, :rating, :customer_id)");
        $stmt->execute([ ':order_id' => $order_id, ':product_id' => $product_id, ':review' => $review, ':rating' => $rating, ':customer_id' => $cust_id ]);
      }

      echo json_encode(['success' => true, 'message' => 'Review submitted or updated successfully']);
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

html, body {
    height: 100%; /* Ensure the body takes the full height of the viewport */
    margin: 0; /* Remove default margins */
    display: flex;
    justify-content: center; /* Horizontally center the content */
    align-items: center; /* Vertically center the content */
}


.horizontal-table {
    display: flex;
    flex-wrap: wrap; /* Allows items to wrap on smaller screens */
    justify-content: center; /* Center items horizontally */
    align-items: center; /* Center items vertically */
    border-radius: 8px;
    margin: auto; /* Centers the container horiz    ontally */
    margin-top: 10rem; /* Adjust vertical spacing */
    width: fit-content; /* Adjust the width to fit content */
    margin-bottom: -5rem;
}

/* Style the links */
.horizontal-table a {
    display: block;
    padding: 10px 28px; /* Increase padding for larger clickable areas */
    margin: 10px; /* Add more spacing between items */
    text-align: center;
    font-size: 1.3rem; /* Make text larger */
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
    transform: scale(1.05); /* Slightly enlarge the link */
}


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
<link rel="stylesheet" href="../css/customerorder.css?v.1.1">
<div class="containers">
  <div class="col-md-12"> 
    <div class="container my-4">
      <h1 class="text-center" >My Orders</h1>
      <div class="table-container">
        <table class="table table-bordered table-hover">
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
            // Query to join the relevant tables and filter by the logged-in customer's ID with pagination
            $stmt = $pdo->prepare("
              SELECT 
                c.cust_id, 
                c.cust_name, 
                c.cust_email, 
                p.name AS product_name, 
                oi.quantity, 
                p.current_price AS unit_price, 
                pay.payment_method, 
                pay.payment_id, 
                pay.created_at AS payment_date, 
                pay.amount_paid, 
                pay.shipping_status, 
                pay.payment_status, 
                o.order_id, 
                p.p_id
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
                  <td>₱<?= number_format($order['amount_paid'], 2) ?></td>
                  <td>
                    <span class="badge <?= $order['payment_status'] === 'pending' ? 'bg-warning' : ($order['payment_status'] === 'paid' ? 'bg-success' : 'bg-danger') ?>">
                      <?= ucfirst($order['payment_status']) ?>
                    </span>
                  </td>
                  <td>
                    <span class="badge <?= $order['shipping_status'] === 'pending' ? 'bg-warning' : ($order['shipping_status'] === 'shipped' ? 'bg-info' : 'bg-success') ?>">
                      <?= ucfirst($order['shipping_status']) ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <div style="display: flex; justify-content: center; align-items: center;">
                      <?php if ($order['shipping_status'] === 'delivered'): ?>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal" data-order-id="<?= $order['order_id'] ?>" data-product-id="<?= $order['p_id'] ?>">Add Review</button>
                      <?php else: ?>
                        <button class="btn btn-secondary" disabled>Add Review</button>
                      <?php endif; ?>
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

  <!-- Modal for Adding Review -->
  <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="reviewModalLabel">Add Review</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="reviewForm">
            <textarea id="review" rows="3" placeholder="Write your review here"></textarea>
            <select id="rating">
              <option value="">Select Rating</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
            </select>
            <input type="hidden" id="order-id">
            <input type="hidden" id="product-id">
            <button type="submit" class="btn btn-primary w-100">Submit Review</button>
          </form>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Load existing review into the modal for editing
  reviewModal.addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const orderId = button.getAttribute('data-order-id');
    const productId = button.getAttribute('data-product-id');

    document.getElementById('order-id').value = orderId;
    document.getElementById('product-id').value = productId;

    // Fetch existing review for the order-product combination
    fetch(`get_review.php?order_id=${orderId}&product_id=${productId}`)
      .then(response => response.json())
      .then(data => {
        document.getElementById('review').value = data.review || '';
        document.getElementById('rating').value = data.rating || '';
      })
      .catch(error => console.error('Error fetching review:', error));
  });

  // Handle review form submission for add/update
  document.getElementById('reviewForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const orderId = document.getElementById('order-id').value;
    const productId = document.getElementById('product-id').value;
    const review = document.getElementById('review').value;
    const rating = document.getElementById('rating').value;

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
        alert('Review updated successfully');
        window.location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    });
  });
</script>

<style>
@import url(https://db.onlinewebfonts.com/c/90ac3b18aaef9f2db3ac8e062c7a033b?family=NudMotoya+Maru+W55+W5);

/* General table container styling */
.table-container {
  max-height: none; /* Remove the max-height to allow the table to grow naturally */
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #f9f9f9;
  margin-top: 20px;
  padding: 10px;
  font-family: "NudMotoya Maru W55 W5"; /* Apply the same font */
  font-size: 12px; /* Smaller font size for the entire table */
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

.btn-primary {
  background-color: #e84393;
  color: white;
  font-size: 12px; /* Smaller font size for buttons */
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
  font-size: 12px; /* Smaller font size for secondary buttons */
  cursor: not-allowed;
  opacity: 0.7;
}

.modal-header {
  color: white;
  font-size: 14px; /* Ensure header font size is appropriate */
}

.modal .btn-primary {
  background-color: #28a745;
  border-color: #28a745;
}

.modal textarea,
.modal select {
  width: 100%;
  border-radius: 5px;
  border: 1px solid #ccc;
  padding: 8px;
  font-size: 12px; /* Smaller font size for inputs */
  margin-bottom: 10px;
}

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
  font-size: 12px; /* Smaller font size for pagination */
}

.pagination a.active {
  background-color: #007bff;
  color: white;
}

.pagination a:hover {
  background-color: #f1f1f1;
}

/* Modal header styling */
#reviewModal .modal-header {

  color: white;
}

/* Modal submit button styling */
#reviewModal .btn-primary {
  background-color: #28a745;
  border-color: #28a745;
  color: white;
  font-size: 16px;
  font-weight: bold;
}

#reviewModal .btn-primary:hover {
  background-color: #218838;
}

/* Modal text area and select dropdown */
#reviewModal textarea,
#reviewModal select {
  width: 100%;
  border-radius: 5px;
  border: 1px solid #ccc;
  padding: 10px;
  font-size: 14px;
  margin-bottom: 10px;
}
</style>


