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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
  try {
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $review = $_POST['review'] ?? null;
    $rating = $_POST['rating'] ?? null;

    // If review and rating are empty, don't insert into the database
    if (!empty($review) && !empty($rating)) {
      // Insert the review into the database
      $stmt = $pdo->prepare("INSERT INTO reviews (order_id, product_id, review, rating, customer_id) VALUES (:order_id, :product_id, :review, :rating, :customer_id)");
      $stmt->execute([
        ':order_id' => $order_id,
        ':product_id' => $product_id,
        ':review' => $review,
        ':rating' => $rating,
        ':customer_id' => $cust_id // Insert the logged-in customer's ID
      ]);
    }

    echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
  } catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  }
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="../css/customerorder.css?v.1.0">
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
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</header>
<div class="container1">
  <div class="col-md-12"> 
    <div class="container my-4">
      <h1 class="text-center">Order Dashboard</h1>
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
            <th>Action</th>
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
                <td>$<?= number_format($order['amount_paid'], 2) ?></td>
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
    // Handle opening the review modal with the correct order and product ID
    const reviewModal = document.getElementById('reviewModal');
    reviewModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget; 
      const orderId = button.getAttribute('data-order-id');
      const productId = button.getAttribute('data-product-id');

      const orderInput = document.getElementById('order-id');
      const productInput = document.getElementById('product-id');

      orderInput.value = orderId;
      productInput.value = productId;
    });

    // Handle the review form submission
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const orderId = document.getElementById('order-id').value;
      const productId = document.getElementById('product-id').value;
      const review = document.getElementById('review').value;
      const rating = document.getElementById('rating').value;

      // Send the review data to the server
      fetch('', {
        method: 'POST',
        body: new URLSearchParams({
          add_review: 'true',
          order_id: orderId,
          product_id: productId,
          review: review,
          rating: rating
        }),
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Review submitted successfully');
          window.location.reload(); // Reload to show updated data
        } else {
          alert('Error: ' + data.message);
        }
      });
    });
  </script>
