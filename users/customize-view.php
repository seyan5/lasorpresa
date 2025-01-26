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

    // Updated SQL query to fetch data grouped by orderitem_id
    $sql = "SELECT 
                o.orderitem_id, 
                o.order_id, 
                o.container_type, 
                o.container_color, 
                o.container_price,
                o.flower_details, 
                o.total_price AS orderitem_total_price,
                cp.payment_status,
                cp.shipping_status,
                ci.expected_image,
                cf.final_image
            FROM custom_orderitems o
            INNER JOIN custom_order coo ON o.order_id = coo.order_id
            LEFT JOIN custom_payment cp ON o.order_id = cp.order_id
            LEFT JOIN custom_images ci ON o.orderitem_id = ci.orderitem_id
            LEFT JOIN custom_finalimages cf ON o.orderitem_id = cf.orderitem_id
            WHERE coo.customer_email = :customer_email
            ORDER BY o.orderitem_id ASC;";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':customer_email' => $cust_email]);

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output styles and structure
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
                    <h3>Order Item ID: {$order['orderitem_id']}</h3>
                    <p><label>Order ID:</label> {$order['order_id']}</p>
                    <p><label>Container Type:</label> {$order['container_type']}</p>
                    <p><label>Container Color:</label> {$order['container_color']}</p>
                    <p><label>Container Price:</label> ₱{$order['container_price']}</p>
                    <p><label>Flower Details:</label> {$order['flower_details']}</p>
                    <p><label>Order Item Total Price:</label> ₱{$order['orderitem_total_price']}</p>
                    <p><label>Payment Status:</label> {$order['payment_status']}</p>
                    <p><label>Shipping Status:</label> {$order['shipping_status']}</p>";

            // Expected image
            if (!empty($order['expected_image'])) {
                echo "<p><strong>Expected Image:</strong></p>";
                echo "<img src='uploads/{$order['expected_image']}' alt='Expected Image'>";
            } else {
                echo "<p>No expected image available.</p>";
            }

            // Final image
            if (!empty($order['final_image'])) {
                echo "<p><strong>Final Image:</strong></p>";
                echo "<img src='../admin/customize/final_image_uploads/{$order['final_image']}' alt='Final Image'>";
            } else {
                echo "<p>No final image available.</p>";
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
