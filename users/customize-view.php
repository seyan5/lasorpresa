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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Custom Orders - Flower Shop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        @import url(https://db.onlinewebfonts.com/c/90ac3b18aaef9f2db3ac8e062c7a033b?family=NudMotoya+Maru+W55+W5);

        :root {
            --primary-pink:rgb(124, 124, 124);
            --secondary-pink:rgb(233, 228, 230);
            --light-pink:rgb(226, 226, 226);
            --accent-green: #4caf50;
            --soft-lavender: #e8eaf6;
            --cream: #fff8e1;
            --gold: #ffd700;
            --text-dark: #2c3e50;
            --text-light: #6c757d;

        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background:rgb(235, 233, 233);
            min-height: 100vh;
            color: var(--text-dark);
            position: relative;
        }

        /* Floating floral background elements */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgb(235, 233, 233);
            pointer-events: none;
            z-index: -1;
        }

        .header {
            background: #fff;
            padding: 2rem 0;
            box-shadow: #333;
            position: relative;
            overflow: hidden;
        }

        .header h1{
            color:#000000;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(45deg);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .page-title {
            font-family: "NudMotoya Maru W55 W5";
            font-size: 2rem;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title i {
            font-size: 2rem;
            color: var(--gold);
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid #000000;
            color: #000000;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .main-container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 0 2rem;
        }

        .orders-grid {
            display: grid;
            gap: 2rem;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        }

        .order-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(233, 30, 99, 0.1);
            position: relative;
            overflow: hidden;
        }

        .order-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;

        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .order-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--light-pink);
        }

        .order-id {
            font-family: "NudMotoya Maru W55 W5";
            font-size: 1.3rem;
            font-weight: 600;
            color: #000000;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-id i {
            color: #000000;
        }

        .status-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-paid { background: #e8f5e8; color: var(--accent-green); }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-shipped { background: #d1ecf1; color: #0c5460; }
        .status-processing { background: #f8d7da; color: #721c24; }

        .order-details {
            display: grid;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            padding: 0.8rem;
            background: var(--light-pink);
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .detail-row:hover {
            background: var(--secondary-pink);
            transform: translateX(5px);
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            background: #000000;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1rem;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .detail-value {
            color: #000000;
            font-size: 1rem;
        }

        .price-highlight {
            color: #000000
            font-weight: 700;
            font-size: 1.2rem;
        }

        .images-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: #000000;
        }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .image-container {
            text-align: center;
        }

        .image-label {
            font-weight: 600;
            color: #000000;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .order-image {
            width: 100%;
            max-width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: #000000;
        }

        .order-image:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(233, 30, 99, 0.3);
        }

        .no-image {
            width: 100%;
            max-width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--light-pink), var(--secondary-pink));
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            font-style: italic;
            border: 2px dashed var(--primary-pink);
        }

        .no-orders {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin: 2rem 0;
        }

        .no-orders i {
            font-size: 4rem;
            color: var(--secondary-pink);
            margin-bottom: 1rem;
        }

        .no-orders h3 {
            font-family: "NudMotoya Maru W55 W5";
            font-size: 2rem;
            color: #000000;
            margin-bottom: 1rem;
            
        }

        .no-orders p {
            color: var(--text-light);
            font-size: 1.1rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .page-title {
                font-size: 2rem;
            }

            .orders-grid {
                grid-template-columns: 1fr;
            }

            .order-card {
                padding: 1.5rem;
            }

            .order-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .images-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 0 1rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .order-card {
                padding: 1rem;
            }
        }

        /* Loading animation for images */
        @keyframes shimmer {
            0% { background-position: -468px 0; }
            100% { background-position: 468px 0; }
        }

        .image-loading {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 400% 100%;
            animation: shimmer 1.5s infinite;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-rose"></i>
                Custom Orders
            </h1>
            <a href="javascript:history.back()" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>
    </header>

    <div class="main-container">
        <?php if (!empty($orders)): ?>
            <div class="orders-grid">
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-id">
                                <i class="fas fa-seedling"></i>
                                Order #<?php echo htmlspecialchars($order['orderitem_id']); ?>
                            </div>
                            <div class="status-badges">
                                <span class="status-badge <?php echo strtolower($order['payment_status']) === 'paid' ? 'status-paid' : 'status-pending'; ?>">
                                    <?php echo htmlspecialchars($order['payment_status']); ?>
                                </span>
                                <span class="status-badge <?php echo strtolower($order['shipping_status']) === 'shipped' ? 'status-shipped' : 'status-processing'; ?>">
                                    <?php echo htmlspecialchars($order['shipping_status']); ?>
                                </span>
                            </div>
                        </div>

                        <div class="order-details">
                            <div class="detail-row">
                                <div class="detail-icon">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Order Reference</div>
                                    <div class="detail-value">#<?php echo htmlspecialchars($order['order_id']); ?></div>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Container Type</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($order['container_type']); ?></div>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-icon">
                                    <i class="fas fa-palette"></i>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Container Color</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($order['container_color']); ?></div>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-icon">
                                    <i class="fas fa-flower"></i>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Floral Arrangement</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($order['flower_details']); ?></div>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-icon">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Container Price</div>
                                    <div class="detail-value price-highlight">₱<?php echo number_format($order['container_price'], 2); ?></div>
                                </div>
                            </div>

                            <div class="detail-row">
                                <div class="detail-icon">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div class="detail-content">
                                    <div class="detail-label">Total Amount</div>
                                    <div class="detail-value price-highlight">₱<?php echo number_format($order['orderitem_total_price'], 2); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="images-section">
                            <div class="images-grid">
                                <div class="image-container">
                                    <div class="image-label">
                                        <i class="fas fa-image"></i>
                                        Your Vision
                                    </div>
                                    <?php if (!empty($order['expected_image'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($order['expected_image']); ?>" 
                                             alt="Expected Design" class="order-image">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-image"></i>
                                            <span>No image provided</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="image-container">
                                    <div class="image-label">
                                        <i class="fas fa-magic"></i>
                                        Our Creation
                                    </div>
                                    <?php if (!empty($order['final_image'])): ?>
                                        <img src="../admin/customize/final_image_uploads/<?php echo htmlspecialchars($order['final_image']); ?>" 
                                             alt="Final Creation" class="order-image">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-clock"></i>
                                            <span>Being crafted with love...</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-seedling"></i>
                <h3>Your Custome Order is Empty</h3>
                <p>You haven't any custom orders yet. Start creating your beautiful floral arrangements!</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Add loading animation to images
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.order-image');
            r
            images.forEach(function(img) {
                img.addEventListener('load', function() {
                    this.style.opacity = '1';
                });
                
                img.addEventListener('error', function() {
                    this.style.opacity = '0.5';
                    this.alt = 'Image not available';
                });
            });
        });

        // Add smooth scroll behavior
        document.documentElement.style.scrollBehavior = 'smooth';
    </script>
</body>
</html>

<?php
} catch (PDOException $e) {
    echo "<div style='padding: 2rem; text-align: center; color: #721c24; background: #f8d7da; border-radius: 10px; margin: 2rem;'>";
    echo "<h3><i class='fas fa-exclamation-triangle'></i> Oops! Something went wrong</h3>";
    echo "<p>We're having trouble loading your orders. Please try again later.</p>";
    echo "</div>";
}
?>