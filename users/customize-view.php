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

    // Display the orders directly on the page
    echo "<style>
            .order-section {
                margin-bottom: 20px;
                border-bottom: 1px solid #555;
                padding-bottom: 10px;
            }
            h2 {
                font-family: Arial, sans-serif;
                color: #333;
            }
            p {
                font-family: Arial, sans-serif;
                color: #555;
            }
            label {
                font-family: Arial, sans-serif;
                color: #555;
                font-weight: bold;
            }
          </style>";

    echo "<h2>My Custom Orders</h2>";

    if (!empty($groupedData)) {
        foreach ($groupedData as $orderId => $details) {
            echo "<div class='order-section'>
                    <h3>Order ID: {$orderId}</h3>
                    <p><label>Container Type:</label> {$details['container_type']}</p>
                    <p><label>Container Color:</label> {$details['container_color']}</p>";
            foreach ($details['flowers'] as $flower) {
                echo "<p><label>Flower Type:</label> {$flower['flower_type']}</p>
                      <p><label>Quantity:</label> {$flower['quantity']}</p>";
            }
            echo "<p><label>Order Total Price:</label> {$details['order_total_price']}</p>
                  </div>";
        }
    } else {
        echo "<p>You have no orders.</p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
