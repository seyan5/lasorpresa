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

    // Display the orders in a styled container
    echo "<style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }

            .header {
                background-color: #f8f8f8;
                padding: 20px 40px;
                display: flex;
                align-items: center;
                justify-content: flex-start;
                border-bottom: 2px solid #ddd;
            }

            .back-link {
                font-size: 18px;
                color: #dd91ad;
                text-decoration: none;
                display: flex;
                align-items: center;
                font-weight: bold;
                transition: color 0.3s ease, transform 0.3s ease;
            }

            .back-link:hover {
                color: #b56e7c;
                transform: translateX(-5px);
            }

            .orders-container {
                width: 80%;
                margin: 30px auto;
                padding: 20px;
                background-color: #ffffff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            h2 {
                font-size: 24px;
                color: #333;
                margin-bottom: 20px;
            }

            .order-section {
                margin-bottom: 20px;
                padding: 15px;
                background-color: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            }

            .order-section:last-child {
                margin-bottom: 0;
            }

            .order-section h3 {
                font-size: 20px;
                color: #333;
                margin-bottom: 10px;
            }

            p, label {
                font-size: 16px;
                color: #555;
                margin: 5px 0;
            }

            label {
                font-weight: bold;
                color: #333;
            }

            .order-section p {
                margin-left: 20px;
            }

            .order-section .flower-info {
                margin-left: 40px;
            }

            .order-section p:last-child {
                font-weight: bold;
                color: #b56e7c;
            }

            /* Add responsiveness for smaller screens */
            @media (max-width: 768px) {
                .orders-container {
                    width: 95%;
                }

                .header {
                    padding: 15px 20px;
                }
            }
        </style>";

    echo "<div class='header'>
            <a href='customer-profile-update.php' class='back-link'>
                <span class='back-arrow'>&lt;</span> Back to Profile
            </a>
          </div>";    

    echo "<div class='orders-container'>";
    echo "<h2>My Custom Orders</h2>";

    if (!empty($groupedData)) {
        foreach ($groupedData as $orderId => $details) {
            echo "<div class='order-section'>
                    <h3>Order ID: {$orderId}</h3>
                    <p><label>Container Type:</label> {$details['container_type']}</p>
                    <p><label>Container Color:</label> {$details['container_color']}</p>";
            foreach ($details['flowers'] as $flower) {
                echo "<div class='flower-info'>
                        <p><label>Flower Type:</label> {$flower['flower_type']}</p>
                        <p><label>Quantity:</label> {$flower['quantity']}</p>
                      </div>";
            }
            echo "<p><label>Order Total Price:</label> {$details['order_total_price']}</p>
                  </div>";
        }
    } else {
        echo "<p>You have no orders.</p>";
    }

    echo "</div>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
