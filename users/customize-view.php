<?php
ob_start();
session_start();
include("../admin/inc/config.php");
include("../admin/inc/functions.php");
include("../admin/inc/CSRF_Protect.php");

// Check if the user is logged in
if (!isset($_SESSION['customer']['cust_email'])) {
    header("Location: login.php");
    exit;
}

try {
    $cust_email = $_SESSION['customer']['cust_email']; // Get the logged-in user's email

    // Fetch data grouped by order_id for the logged-in customer
    $sql = "SELECT 
                o.order_id, 
                o.container_type, 
                o.container_color, 
                o.flower_type, 
                o.num_flowers, 
                co.total_price AS order_total_price
            FROM custom_orderitems o
            INNER JOIN custom_order co ON o.order_id = co.order_id
            WHERE co.customer_email = :customer_email
            ORDER BY o.order_id ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':customer_email' => $cust_email]);

    // Group data by Order ID
    $groupedData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!isset($groupedData[$row['order_id']])) {
            $groupedData[$row['order_id']] = [
                'container_type' => $row['container_type'],
                'container_color' => $row['container_color'],
                'flowers' => [],
                'order_total_price' => $row['order_total_price']
            ];
        }
        $groupedData[$row['order_id']]['flowers'][] = [
            'flower_type' => $row['flower_type'],
            'quantity' => $row['num_flowers']
        ];
    }

    // Display the modal trigger button and modal
    echo "<style>
            .modal {
                display: none; 
                position: fixed; 
                z-index: 1; 
                left: 0; 
                top: 0; 
                width: 100%; 
                height: 100%; 
                overflow: auto; 
                background-color: rgba(0, 0, 0, 0.4); 
            }
            .modal-content {
                background-color: #333;
                color: #fff;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                border-radius: 10px;
                font-family: Arial, sans-serif;
            }
            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }
            .close:hover,
            .close:focus {
                color: white;
                text-decoration: none;
                cursor: pointer;
            }
            .order-section {
                margin-bottom: 20px;
                border-bottom: 1px solid #555;
                padding-bottom: 10px;
            }
          </style>";

    echo "<button id='openModal'>View My Orders</button>";

    // Modal structure
    echo "<div id='myModal' class='modal'>
            <div class='modal-content'>
                <span class='close'>&times;</span>
                <h2>My Orders</h2>";

    if (!empty($groupedData)) {
        foreach ($groupedData as $orderId => $details) {
            echo "<div class='order-section'>
                    <h3>Order ID: {$orderId}</h3>
                    <p><strong>Container Type:</strong> {$details['container_type']}</p>
                    <p><strong>Container Color:</strong> {$details['container_color']}</p>";
            foreach ($details['flowers'] as $flower) {
                echo "<p><strong>Flower Type:</strong> {$flower['flower_type']}</p>
                      <p><strong>Quantity:</strong> {$flower['quantity']}</p>";
            }
            echo "<p><strong>Order Total Price:</strong> {$details['order_total_price']}</p>
                  </div>";
        }
    } else {
        echo "<p>You have no orders.</p>";
    }

    echo "  </div>
          </div>";

    // Modal JavaScript
    echo "<script>
            var modal = document.getElementById('myModal');
            var btn = document.getElementById('openModal');
            var span = document.getElementsByClassName('close')[0];

            btn.onclick = function() {
                modal.style.display = 'block';
            }

            span.onclick = function() {
                modal.style.display = 'none';
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
          </script>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
