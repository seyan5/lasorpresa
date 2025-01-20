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

    // Fetch data grouped by order_id for the logged-in customer, including payment and shipping statuses
    $sql = "SELECT 
                o.order_id, 
                o.container_type, 
                o.container_color, 
                GROUP_CONCAT(DISTINCT CONCAT(o.flower_type, ' (', o.num_flowers, ')') SEPARATOR ', ') AS flower_details, 
                SUM(co.total_price) AS order_total_price,
                cp.payment_status,
                cp.shipping_status,
                GROUP_CONCAT(DISTINCT ci.expected_image SEPARATOR ', ') AS expected_images,
                GROUP_CONCAT(DISTINCT cf.final_image SEPARATOR ', ') AS final_images
            FROM custom_orderitems o
            INNER JOIN custom_order co ON o.order_id = co.order_id
            LEFT JOIN custom_payment cp ON o.order_id = cp.order_id
            LEFT JOIN custom_images ci ON o.order_id = ci.order_id
            LEFT JOIN custom_finalimages cf ON o.order_id = cf.order_id
            WHERE co.customer_email = :customer_email
            GROUP BY o.order_id
            ORDER BY o.order_id ASC;";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':customer_email' => $cust_email]);

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

            img {
                max-width: 10%; 
                display: block;
                margin: 10px 0;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
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

    if (!empty($orders)) {
        foreach ($orders as $order) {
            echo "<div class='order-section'>
                    <h3>Order ID: {$order['order_id']}</h3>
                    <p><label>Container Type:</label> {$order['container_type']}</p>
                    <p><label>Container Color:</label> {$order['container_color']}</p>";

            // Check if flower_details is available
            if (!empty($order['flower_details'])) {
                $flowerDetails = explode(", ", $order['flower_details']);
                foreach ($flowerDetails as $flowerDetail) {
                    echo "<p><label>Flower:</label> {$flowerDetail}</p>";
                }
            } else {
                echo "<p>No flowers available for this order.</p>";
            }

            echo "<p><label>Order Total Price:</label> ₱{$order['order_total_price']}</p>";

            // Add payment and shipping statuses
            echo "<p><label>Payment Status:</label> {$order['payment_status']}</p>";
            echo "<p><label>Shipping Status:</label> {$order['shipping_status']}</p>";

            // Display the expected images
            if (!empty($order['expected_images'])) {
                echo "<p><strong>Expected Images:</strong></p>";
                $expectedImages = explode(", ", $order['expected_images']);
                foreach ($expectedImages as $image) {
                    echo "<img src='uploads/{$image}' alt='Expected Image'>";
                }
            } else {
                echo "<p>No expected images available.</p>";
            }

            // Display the final images
            if (!empty($order['final_images'])) {
                echo "<p><strong>Final Images:</strong></p>";
                $finalImages = explode(", ", $order['final_images']);
                foreach ($finalImages as $image) {
                    echo "<img src='../admin/customize/final_image_uploads/{$image}' alt='Final Image'>";
                }
            } else {
                echo "<p>No final images available.</p>";
            }

            echo "</div>";
        }
    } else {
        echo "<p>You have no orders.</p>";
    }

    echo "</div>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
