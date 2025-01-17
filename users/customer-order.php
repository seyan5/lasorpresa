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
      $stmt->execute([
        ':order_id' => $order_id,
        ':product_id' => $product_id,
        ':customer_id' => $cust_id
      ]);
      
      $existing_review = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($existing_review) {
        // Update the existing review
        $stmt = $pdo->prepare("UPDATE reviews SET review = :review, rating = :rating WHERE review_id = :review_id");
        $stmt->execute([
          ':review' => $review,
          ':rating' => $rating,
          ':review_id' => $existing_review['review_id']
        ]);
      } else {
        // Insert new review
        $stmt = $pdo->prepare("INSERT INTO reviews (order_id, product_id, review, rating, customer_id) VALUES (:order_id, :product_id, :review, :rating, :customer_id)");
        $stmt->execute([
          ':order_id' => $order_id,
          ':product_id' => $product_id,
          ':review' => $review,
          ':rating' => $rating,
          ':customer_id' => $cust_id
        ]);
      }

      echo json_encode(['success' => true, 'message' => 'Review submitted or updated successfully']);
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/customerorder.css?">
    <title>Navbar Fix</title>
</head>
<body>
<header>
    <img src="../images/logo.png" alt="Logo" class="logos">
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="customer-profile-update.php">Update Profile</a>
        <a href="customer-password-update.php">Update Password</a>
        <a href="customer-order.php">Orders</a>
        <a href="customize-view.php">Custom Orders</a>
    </nav>
    <div class="icons">
            <a href="shopcart.php" class="fas fa-shopping-cart"></a>
            <div class="user-dropdown">
                <a href="#" class="fas fa-user" onclick="toggleDropdown()"></a>
                <div class="dropdown-menu" id="userDropdown">
                    <?php if (isset($_SESSION['customer'])): ?>
                        <p>Welcome, <?php echo htmlspecialchars($_SESSION['customer']['cust_name']); ?></p>
                        <hr>
                        <a href="customer-profile-update.php">Profile</a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="notification-dropdown">
                <a href="#" class="fas fa-bell" onclick="toggleNotificationDropdown()"></a>
                <div class="dropdown-menu" id="notificationDropdown">
                    <?php 
                    // Check if a customer is logged in
                    if (isset($_SESSION['customer']) && isset($_SESSION['customer']['cust_id'])) {
                        $customerId = $_SESSION['customer']['cust_id']; // Get the logged-in customer's ID

                        // Fetch payments for the logged-in customer with the necessary conditions
                        $statement = $pdo->prepare("
                            SELECT p.*, oi.product_id, pr.name
                            FROM payment p
                            JOIN order_items oi ON p.order_id = oi.order_id
                            JOIN product pr ON oi.product_id = pr.p_id
                            WHERE p.cust_id = :cust_id
                            AND (p.payment_status = 'pending' OR p.shipping_status != 'delivered') 
                            ORDER BY p.created_at DESC
                        ");
                        $statement->execute(['cust_id' => $customerId]);
                        $payments = $statement->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($payments)): 
                    ?>
                        <p>Notifications</p>
                        <hr>
                        <?php foreach ($payments as $payment): ?>
                            <?php 
                            // Determine payment status message and shipping status message
                            $paymentStatus = ($payment['payment_status'] == 'pending') ? 'Payment Pending' : ($payment['payment_status'] == 'paid' ? 'Payment Confirmed' : 'Payment Failed');
                            $shippingStatus = ($payment['shipping_status'] == 'pending') ? 'Shipping Pending' : ($payment['shipping_status'] == 'shipped' ? 'Shipped' : 'Delivered');
                            ?>
                            <li class="dropdown-item d-flex align-items-center">
                                <i class="fa fa-credit-card me-2 <?php echo $payment['payment_status'] == 'pending' ? 'bg-warning' : 'bg-success'; ?>" style="padding: 5px; border-radius: 50%;"></i>
                                <div>
                                    <a href="order-details.php?order_id=<?php echo $payment['order_id']; ?>&product_id=<?php echo $payment['product_id']; ?>" style="text-decoration: none;">
                                        <strong>Product: <?php echo $payment['name']; ?></strong>
                                        <div class="text-muted small"><?php echo $paymentStatus; ?></div>
                                        <div class="text-muted small"><?php echo $shippingStatus; ?></div>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        <hr>
                        <a href="notifications.php" class="btn btn-link">View All</a>
                    <?php else: ?>
                        <li>
                            <span class="dropdown-item text-center text-muted">No new notifications</span>
                        </li>
                    <?php endif; ?>
                    <?php 
                    } else { 
                    ?>
                        <li>
                            <span class="dropdown-item text-center text-muted">No customer logged in</span>
                        </li>
                    <?php } ?>
                </div>
            </div>
        </div>
</div>
</div>
</header>
<div class="container1">
  <div class="col-md-12"> 
    <div class="container my-4">
      <h1 class="text-center">My Orders</h1>
      <div class="table-container">
        <table class="table table-bordered table-hover">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Product Details</th>
              <th>Payment Information</th>
              <th>Paid Amount</th>
              <th>Payment Status</th>
              <th>Shipping Status</th>
              <th >Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Query to join the relevant tables and filter by the logged-in customer's ID
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
                    ");

            // Execute query
            $stmt->execute([':cust_id' => $cust_id]);

            // Fetch orders
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($orders)) {
              echo "<tr><td colspan='8' class='text-center'>No orders found</td></tr>";
            } else {
              foreach ($orders as $index => $order) {
                ?>
                <tr>
                  <td><?= $index + 1 ?></td>
                  <td>
                    <strong>Id:</strong> <?= $order['cust_id'] ?><br>
                    <strong>Name:</strong> <?= htmlspecialchars($order['cust_name']) ?><br>
                    <strong>Email:</strong> <?= htmlspecialchars($order['cust_email']) ?>
                  </td>
                  <td>
                    <strong>Product:</strong> <?= htmlspecialchars($order['product_name']) ?><br>
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
                  <td>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal" data-order-id="<?= $order['order_id'] ?>" data-product-id="<?= $order['p_id'] ?>">Add Review</button>
                  </td>
                </tr>
              <?php }
            } ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal for Adding Review -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="reviewModalLabel">Add Review</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
          </div>
          <div class="modal-body">
            <form id="reviewForm">
              <div class="mb-3">
                <label for="review" class="form-label">Review</label>
                <textarea class="form-control" id="review" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select class="form-select" id="rating">
                  <option value="">Select Rating (Optional)</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                </select>
              </div>
              <input type="hidden" id="order-id">
              <input type="hidden" id="product-id">
              <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
          </div>
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

    function toggleDropdown() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }

    // Close the dropdown when clicking outside
    window.onclick = function(event) {
        if (!event.target.matches('.fa-user')) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    };

  </script>
<style>
  /* Add these styles to make the table scrollable */
.table-container {
  max-height: 200px; /* You can adjust the height as needed */
  overflow-y: auto;
}
</style>