<?php
include('auth.php'); // Ensure the admin is logged in
include('header.php');
include_once('conn.php');

// Set default sorting order
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'DESC';
$order_by = strtoupper($order_by) === 'ASC' ? 'ASC' : 'DESC';  // Default to DESC if not specified or invalid

// Fetch wishlist items and count of customers who have added each item to their wishlist
$stmt = $pdo->prepare("
    SELECT 
        p.p_id,
        p.name AS product_name,
        p.current_price,
        p.featured_photo,  -- Add the featured_photo column
        COUNT(w.p_id) AS product_count
    FROM wishlist w
    JOIN product p ON w.p_id = p.p_id
    GROUP BY w.p_id
    ORDER BY product_count $order_by
");

$stmt->execute();
$wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bloom & Bliss - Wishlist Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #ffeef8 0%, #f0f9ff 50%, #ecfdf5 100%);
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        /* Floral background decoration */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(255, 182, 193, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(144, 238, 144, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(255, 218, 185, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 
                0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-section {
            text-align: center;
            margin-bottom: 50px;
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 0;
            left: 0;
            background: #de91ad;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
            text-decoration: none;
        }

        .back-button:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 20px rgba(236, 72, 153, 0.4);
            background: #de91ad;
        }

        .back-button:active {
            transform: translateY(0) scale(1.02);
        }

        .back-button i {
            font-size: 1rem;
            transition: transform 0.3s ease;
        }

        .back-button:hover i {
            transform: translateX(-3px);
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2.5rem;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateX(-50%) translateY(0px); }
            50% { transform: translateX(-50%) translateY(-10px); }
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            font-weight: 600;
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 50%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .subtitle {
            color: #6b7280;
            font-size: 1.1rem;
            font-weight: 400;
            opacity: 0.8;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, #fdf2f8 0%, #fef3c7 100%);
            padding: 25px;
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(236, 72, 153, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(236, 72, 153, 0.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #ec4899;
        }

        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(236, 72, 153, 0.1);
        }

        .table-header {
            background: #de91ad;
            padding: 20px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sort-controls {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
        }

        .sort-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .sort-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .sort-btn.active {
            background: white;
            color: #ec4899;
        }

        .wishlist-table {
            width: 100%;
            border-collapse: collapse;
        }

        .wishlist-table th {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            padding: 20px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .wishlist-table td {
            padding: 25px 20px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .wishlist-table tr {
            transition: all 0.3s ease;
        }

        .wishlist-table tr:hover {
            background: linear-gradient(135deg, #fdf2f8 0%, #f0f9ff 100%);
            transform: scale(1.01);
        }

        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .product-name {
            font-weight: 600;
            color: #1f2937;
            font-size: 1.1rem;
        }

        .price {
            color: #de91ad;
            font-weight: 700;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .count-badge {
            background: linear-gradient(135deg, #ec4899 0%, #f97316 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 8px rgba(236, 72, 153, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ec4899;
            margin-bottom: 20px;
            opacity: 0.7;
        }

        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #374151;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }

            h1 {
                font-size: 2rem;
            }

            .header-section {
                margin-bottom: 40px;
            }

            .back-button {
                position: relative;
                margin-bottom: 20px;
                align-self: flex-start;
            }

            .table-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .wishlist-table {
                font-size: 0.9rem;
            }

            .wishlist-table th,
            .wishlist-table td {
                padding: 15px 10px;
            }

            .product-image {
                width: 60px;
                height: 60px;
            }
        }

        /* Loading animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-section">
            <button class="back-button" onclick="goBack()">
                <i class="fas fa-arrow-left"></i>
                Back
            </button>
            <h1>Lasorpresa</h1>
            <p class="subtitle">Most loved flowers by your customers</p>
        </div>

        <?php if (count($wishlist_items) > 0) : ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= count($wishlist_items) ?></div>
                    <div class="stat-label">Unique Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= array_sum(array_column($wishlist_items, 'product_count')) ?></div>
                    <div class="stat-label">Total Wishes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= max(array_column($wishlist_items, 'product_count')) ?></div>
                    <div class="stat-label">Most Popular</div>
                </div>
            </div>

            <div class="table-container">
                <div class="table-header">
                    <div class="table-title">
                        <i class="fas fa-heart"></i>
                        Customer Favorites
                    </div>
                    <div class="sort-controls">
                        <span>Sort by popularity:</span>
                        <button class="sort-btn <?= $order_by == 'DESC' ? 'active' : '' ?>" onclick="setSortOrder('DESC')">
                            <i class="fas fa-arrow-down"></i>
                            High to Low
                        </button>
                        <button class="sort-btn <?= $order_by == 'ASC' ? 'active' : '' ?>" onclick="setSortOrder('ASC')">
                            <i class="fas fa-arrow-up"></i>
                            Low to High
                        </button>
                    </div>
                </div>

                <table class="wishlist-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-image"></i> Photo</th>
                            <th><i class="fas fa-seedling"></i> Product Name</th>
                            <th><i class="fas fa-tag"></i> Price</th>
                            <th><i class="fas fa-heart"></i> Popularity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($wishlist_items as $index => $item) : ?>
                            <tr style="animation-delay: <?= $index * 0.1 ?>s;">
                                <td>
                                    <?php if (!empty($item['featured_photo'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($item['featured_photo']) ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>" 
                                             class="product-image">
                                    <?php else: ?>
                                        <img src="uploads/default.jpg" alt="Default Flower" class="product-image">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                </td>
                                <td>
                                    <div class="price">
                                        <i class="fas fa-peso-sign"></i>
                                        <?= number_format($item['current_price'], 2) ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="count-badge">
                                        <i class="fas fa-heart"></i>
                                        <?= $item['product_count'] ?> <?= $item['product_count'] == 1 ? 'customer' : 'customers' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="table-container">
                <div class="empty-state">
                    <i class="fas fa-heart-broken"></i>
                    <h3>No Wishlist Items Yet</h3>
                    <p>When customers start adding flowers to their wishlist, they'll appear here.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        let isLoading = false;

        function goBack() {
            // Check if there's a previous page in history
            if (document.referrer && document.referrer !== window.location.href) {
                window.history.back();
            } else {
                // Fallback to a default admin page or dashboard
                window.location.href = 'dashboard.php'; // Change this to your admin dashboard URL
            }
        }

        function setSortOrder(order) {
            if (isLoading) return;
            
            isLoading = true;
            const activeBtn = document.querySelector('.sort-btn.active');
            if (activeBtn) {
                activeBtn.innerHTML = '<div class="loading"></div> Loading...';
            }
            
            setTimeout(() => {
                window.location.href = `?order_by=${order}`;
            }, 500);
        }

        // Add entrance animation
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('.wishlist-table tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Add smooth scrolling for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>