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

<?php include('profnav.php'); ?>
<link rel="stylesheet" href="../css/profileupd.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="../css/dropdown.css">
    <link rel="stylesheet" href="../css/customerorder.css">

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
    ORDER BY pay.created_at DESC
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
  /* General table container styling */
.table-container {
  max-height: 400px; /* Adjust height as needed */
  overflow-y: auto;
  border: 1px solid #ddd; /* Add a border for a cleaner look */
  border-radius: 5px;
  background-color: #f9f9f9; /* Light background for contrast */
  margin-top: 20px;
  padding: 10px;
}

/* Table header styling */
.table thead th {
  background-color: #e84393;
  color: white;
  text-align: center;
  font-weight: bold;
}

/* Table row hover effect */
.table tbody tr:hover {
  background-color: #f1f1f1;
}

/* Add Review and disabled button styling */
.btn-primary {
  background-color: #e84393;
  border-color: ##e84393;
  color: white;
  font-size: 14px;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.btn-primary:hover {
  background-color: #0056b3;
}

.btn-secondary {
  background-color: #6c757d;
  border-color: #6c757d;
  color: white;
  font-size: 14px;
  cursor: not-allowed;
  opacity: 0.7;
}

/* Modal header styling */
#reviewModal .modal-header {
  background-color: #e84393;
  color: white;
  border-bottom: 2px solid #e84393;
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

/* Scrollbar styling for the table container */
.table-container::-webkit-scrollbar {
  width: 10px;
}

.table-container::-webkit-scrollbar-thumb {
  background: #e84393;
  border-radius: 10px;
}

.table-container::-webkit-scrollbar-thumb:hover {
  background: #e84393;
}

/* Responsive layout for smaller screens */
@media (max-width: 768px) {
  .table-container {
    max-height: none;
    overflow-y: visible;
  }

  .table th, .table td {
    font-size: 12px;
    padding: 8px;
  }

  .btn-primary,
  .btn-secondary {
    font-size: 12px;
    padding: 6px 12px;
  }

  #reviewModal textarea,
  #reviewModal select {
    font-size: 12px;
  }
}
/* Center align buttons inside the table cell */
.table td {
  vertical-align: middle;
  text-align: center;
}

.table-container .btn-primary,
.table-container .btn-secondary {
  display: inline-block;
  margin: 0 auto;
}


</style>