<?php
include('conn.php');

$dateFilter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$productTypeFilter = isset($_GET['product_type']) ? $_GET['product_type'] : 'all';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

switch ($dateFilter) {
    case 'daily':
        $interval = "DATE(pay.created_at) = CURDATE()";
        break;
    case 'weekly':
        $interval = "YEARWEEK(pay.created_at, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'monthly':
        $interval = "MONTH(pay.created_at) = MONTH(CURDATE()) AND YEAR(pay.created_at) = YEAR(CURDATE())";
        break;
    default:
        $interval = "1";
}

$monthCondition = '';
$yearCondition = '';

if (isset($_GET['month']) && $_GET['month'] !== 'all') {
    $monthCondition = "AND MONTH(pay.created_at) = :month";
}

if (isset($_GET['year']) && $_GET['year'] !== 'all') {
    $yearCondition = "AND YEAR(pay.created_at) = :year";
}

if ($productTypeFilter !== 'all') {
    $productTypeCondition = " AND ec.ecat_id = :product_type";
} else {
    $productTypeCondition = "";
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.name AS product_name, 
            p.ecat_id,
            oi.quantity, 
            p.current_price, 
            pay.amount_paid, 
            pay.created_at AS payment_date,
            ec.ecat_name AS product_type
        FROM payment pay
        JOIN orders o ON pay.order_id = o.order_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.p_id
        JOIN end_category ec ON p.ecat_id = ec.ecat_id
        WHERE $interval $productTypeCondition $monthCondition $yearCondition
        ORDER BY pay.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    if (isset($_GET['month']) && $_GET['month'] !== 'all') {
        $stmt->bindParam(':month', $_GET['month'], PDO::PARAM_INT);
    }

    if (isset($_GET['year']) && $_GET['year'] !== 'all') {
        $stmt->bindParam(':year', $_GET['year'], PDO::PARAM_INT);
    }

    if ($productTypeFilter !== 'all') {
        $stmt->bindParam(':product_type', $productTypeFilter, PDO::PARAM_INT);
    }

    $stmt->execute();
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM payment pay JOIN orders o ON pay.order_id = o.order_id JOIN order_items oi ON o.order_id = oi.order_id JOIN product p ON oi.product_id = p.p_id JOIN end_category ec ON p.ecat_id = ec.ecat_id WHERE $interval $productTypeCondition $monthCondition $yearCondition");
    
    if (isset($_GET['month']) && $_GET['month'] !== 'all') {
        $countStmt->bindParam(':month', $_GET['month'], PDO::PARAM_INT);
    }

    if (isset($_GET['year']) && $_GET['year'] !== 'all') {
        $countStmt->bindParam(':year', $_GET['year'], PDO::PARAM_INT);
    }

    if ($productTypeFilter !== 'all') {
        $countStmt->bindParam(':product_type', $productTypeFilter, PDO::PARAM_INT);
    }

    $countStmt->execute();
    $totalSales = $countStmt->fetchColumn();
    $totalPages = ceil($totalSales / $perPage);

    $totalSalesStmt = $pdo->prepare("
        SELECT 
            SUM(oi.quantity) AS total_quantity, 
            SUM(pay.amount_paid) AS total_sales,
            ec.ecat_name AS product_type
        FROM payment pay
        JOIN orders o ON pay.order_id = o.order_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.p_id
        JOIN end_category ec ON p.ecat_id = ec.ecat_id
        WHERE $interval $monthCondition $yearCondition
        GROUP BY ec.ecat_id
    ");
    if (isset($_GET['month']) && $_GET['month'] !== 'all') {
        $totalSalesStmt->bindParam(':month', $_GET['month'], PDO::PARAM_INT);
    }

    if (isset($_GET['year']) && $_GET['year'] !== 'all') {
        $totalSalesStmt->bindParam(':year', $_GET['year'], PDO::PARAM_INT);
    }

    $totalSalesStmt->execute();
    $totalSalesData = $totalSalesStmt->fetchAll(PDO::FETCH_ASSOC);

    $overallTotalStmt = $pdo->prepare("
        SELECT 
            SUM(oi.quantity) AS total_quantity, 
            SUM(pay.amount_paid) AS total_sales
        FROM payment pay
        JOIN orders o ON pay.order_id = o.order_id
        JOIN order_items oi ON o.order_id = oi.order_id
        JOIN product p ON oi.product_id = p.p_id
        WHERE $interval $monthCondition $yearCondition
    ");
    if (isset($_GET['month']) && $_GET['month'] !== 'all') {
        $overallTotalStmt->bindParam(':month', $_GET['month'], PDO::PARAM_INT);
    }

    if (isset($_GET['year']) && $_GET['year'] !== 'all') {
        $overallTotalStmt->bindParam(':year', $_GET['year'], PDO::PARAM_INT);
    }

    $overallTotalStmt->execute();
    $overallTotalData = $overallTotalStmt->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    echo "Error fetching sales data: " . $e->getMessage();
}

$productTypesStmt = $pdo->prepare("SELECT * FROM end_category");
$productTypesStmt->execute();
$productTypes = $productTypesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="header-left">
                    <a href="dashboard.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        <span>Dashboard</span>
                    </a>
                </div>
                <div class="header-center">
                    <h1 class="main-title">
                        <i class="fas fa-chart-line"></i>
                        Sales Analytics
                    </h1>
                    <p class="subtitle">Advanced Sales Reporting & Insights</p>
                </div>
                <div class="header-right">
                    <button class="print-btn" onclick="handlePrint()">
                        <i class="fas fa-print"></i>
                        Export Report
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card total-sales">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Revenue</h3>
                        <p class="stat-value">₱<?= number_format($overallTotalData['total_sales'] ?? 0, 2) ?></p>
                        <span class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i> 12.5%
                        </span>
                    </div>
                </div>
                
                <div class="stat-card total-quantity">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Items Sold</h3>
                        <p class="stat-value"><?= number_format($overallTotalData['total_quantity'] ?? 0) ?></p>
                        <span class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i> 8.2%
                        </span>
                    </div>
                </div>
                
                <div class="stat-card total-orders">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Total Orders</h3>
                        <p class="stat-value"><?= count($sales) ?></p>
                        <span class="stat-trend negative">
                            <i class="fas fa-arrow-down"></i> 2.1%
                        </span>
                    </div>
                </div>
                
                <div class="stat-card avg-order">
                    <div class="stat-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Avg. Order Value</h3>
                        <p class="stat-value">₱<?= count($sales) > 0 ? number_format(($overallTotalData['total_sales'] ?? 0) / count($sales), 2) : '0.00' ?></p>
                        <span class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i> 5.7%
                        </span>
                    </div>
                </div>
            </div>

            <!-- Filter Controls -->
            <div class="controls-panel">
                <div class="filter-section">
                    <h3><i class="fas fa-filter"></i> Filter Options</h3>
                    
                    <!-- Time Period Filters -->
                    <div class="filter-group">
                        <label>Time Period:</label>
                        <div class="btn-group">
                            <a href="sales-report.php?filter=daily&product_type=<?= $productTypeFilter ?>&month=<?= $_GET['month'] ?? 'all' ?>&year=<?= $_GET['year'] ?? 'all' ?>" 
                               class="filter-btn <?= $dateFilter === 'daily' ? 'active' : '' ?>">Daily</a>
                            <a href="sales-report.php?filter=weekly&product_type=<?= $productTypeFilter ?>&month=<?= $_GET['month'] ?? 'all' ?>&year=<?= $_GET['year'] ?? 'all' ?>" 
                               class="filter-btn <?= $dateFilter === 'weekly' ? 'active' : '' ?>">Weekly</a>
                            <a href="sales-report.php?filter=monthly&product_type=<?= $productTypeFilter ?>&month=<?= $_GET['month'] ?? 'all' ?>&year=<?= $_GET['year'] ?? 'all' ?>" 
                               class="filter-btn <?= $dateFilter === 'monthly' ? 'active' : '' ?>">Monthly</a>
                            <a href="sales-report.php?filter=all&product_type=<?= $productTypeFilter ?>&month=<?= $_GET['month'] ?? 'all' ?>&year=<?= $_GET['year'] ?? 'all' ?>" 
                               class="filter-btn <?= $dateFilter === 'all' ? 'active' : '' ?>">All Time</a>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <form class="advanced-filters" action="sales-report.php" method="GET">
                        <input type="hidden" name="filter" value="<?= $dateFilter ?>">
                        
                        <div class="filter-item">
                            <label for="month">Month:</label>
                            <select name="month" id="month" onchange="this.form.submit()">
                                <option value="all" <?= !isset($_GET['month']) || $_GET['month'] === 'all' ? 'selected' : '' ?>>All Months</option>
                                <option value="1" <?= isset($_GET['month']) && $_GET['month'] === '1' ? 'selected' : '' ?>>January</option>
                                <option value="2" <?= isset($_GET['month']) && $_GET['month'] === '2' ? 'selected' : '' ?>>February</option>
                                <option value="3" <?= isset($_GET['month']) && $_GET['month'] === '3' ? 'selected' : '' ?>>March</option>
                                <option value="4" <?= isset($_GET['month']) && $_GET['month'] === '4' ? 'selected' : '' ?>>April</option>
                                <option value="5" <?= isset($_GET['month']) && $_GET['month'] === '5' ? 'selected' : '' ?>>May</option>
                                <option value="6" <?= isset($_GET['month']) && $_GET['month'] === '6' ? 'selected' : '' ?>>June</option>
                                <option value="7" <?= isset($_GET['month']) && $_GET['month'] === '7' ? 'selected' : '' ?>>July</option>
                                <option value="8" <?= isset($_GET['month']) && $_GET['month'] === '8' ? 'selected' : '' ?>>August</option>
                                <option value="9" <?= isset($_GET['month']) && $_GET['month'] === '9' ? 'selected' : '' ?>>September</option>
                                <option value="10" <?= isset($_GET['month']) && $_GET['month'] === '10' ? 'selected' : '' ?>>October</option>
                                <option value="11" <?= isset($_GET['month']) && $_GET['month'] === '11' ? 'selected' : '' ?>>November</option>
                                <option value="12" <?= isset($_GET['month']) && $_GET['month'] === '12' ? 'selected' : '' ?>>December</option>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="year">Year:</label>
                            <select name="year" id="year" onchange="this.form.submit()">
                                <option value="all" <?= !isset($_GET['year']) || $_GET['year'] === 'all' ? 'selected' : '' ?>>All Years</option>
                                <?php
                                $currentYear = date('Y');
                                for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                    echo '<option value="' . $i . '" ' . (isset($_GET['year']) && $_GET['year'] == $i ? 'selected' : '') . '>' . $i . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="product_type">Product Type:</label>
                            <select name="product_type" id="product_type" onchange="this.form.submit()">
                                <option value="all" <?= $productTypeFilter === 'all' ? 'selected' : '' ?>>All Products</option>
                                <?php foreach ($productTypes as $productType) { ?>
                                    <option value="<?= $productType['ecat_id'] ?>" <?= $productTypeFilter == $productType['ecat_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($productType['ecat_name']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-section">
                <div class="chart-container">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-area"></i> Sales Trend</h3>
                        <div class="chart-controls">
                            <button class="chart-type-btn active" data-type="line">Line</button>
                            <button class="chart-type-btn" data-type="bar">Bar</button>
                            <button class="chart-type-btn" data-type="area">Area</button>
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>

                <div class="chart-container">
                    <div class="chart-header">
                        <h3><i class="fas fa-chart-pie"></i> Product Categories</h3>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="table-section">
                <div class="table-header">
                    <h3><i class="fas fa-table"></i> Detailed Sales Report</h3>
                    <div class="table-actions">
                        <button class="action-btn" onclick="exportToCSV()">
                            <i class="fas fa-download"></i> Export CSV
                        </button>
                        <button class="action-btn" onclick="toggleFullscreen()">
                            <i class="fas fa-expand"></i> Fullscreen
                        </button>
                    </div>
                </div>

                <?php if (empty($sales)) { ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>No Sales Data Found</h4>
                        <p>No sales records match your current filters. Try adjusting your search criteria.</p>
                    </div>
                <?php } else { ?>
                    <div class="table-container">
                        <table class="sales-table">
                            <thead>
                                <tr>
                                    <th><i class="fas fa-tag"></i> Product Type</th>
                                    <th><i class="fas fa-box"></i> Product Name</th>
                                    <th><i class="fas fa-sort-numeric-up"></i> Quantity</th>
                                    <th><i class="fas fa-dollar-sign"></i> Unit Price</th>
                                    <th><i class="fas fa-credit-card"></i> Amount Paid</th>
                                    <th><i class="fas fa-calendar"></i> Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $index => $sale) { ?>
                                    <tr class="table-row" style="animation-delay: <?= $index * 0.1 ?>s">
                                        <td>
                                            <span class="category-tag"><?= htmlspecialchars($sale['product_type']) ?></span>
                                        </td>
                                        <td class="product-name"><?= htmlspecialchars($sale['product_name']) ?></td>
                                        <td>
                                            <span class="quantity-badge"><?= $sale['quantity'] ?></span>
                                        </td>
                                        <td class="price">₱<?= number_format($sale['current_price'], 2) ?></td>
                                        <td class="amount-paid">₱<?= number_format($sale['amount_paid'], 2) ?></td>
                                        <td class="date"><?= date('M d, Y H:i', strtotime($sale['payment_date'])) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <!-- Category Breakdown -->
            <div class="category-breakdown">
                <h3><i class="fas fa-chart-bar"></i> Sales by Category</h3>
                <div class="category-grid">
                    <?php foreach ($totalSalesData as $index => $data) { ?>
                        <div class="category-card" style="animation-delay: <?= $index * 0.2 ?>s">
                            <div class="category-icon">
                                <i class="fas fa-cube"></i>
                            </div>
                            <div class="category-info">
                                <h4><?= htmlspecialchars($data['product_type']) ?></h4>
                                <div class="category-stats">
                                    <div class="stat">
                                        <span class="label">Items Sold:</span>
                                        <span class="value"><?= number_format($data['total_quantity']) ?></span>
                                    </div>
                                    <div class="stat">
                                        <span class="label">Revenue:</span>
                                        <span class="value">₱<?= number_format($data['total_sales'], 2) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="category-progress">
                                <div class="progress-bar" style="width: <?= ($data['total_sales'] / ($overallTotalData['total_sales'] ?? 1)) * 100 ?>%"></div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1) { ?>
                <div class="pagination-container">
                    <div class="pagination">
                        <a href="sales-report.php?filter=<?= $dateFilter ?>&page=<?= max(1, $page - 1) ?>&product_type=<?= $productTypeFilter ?>&month=<?= $_GET['month'] ?? 'all' ?>&year=<?= $_GET['year'] ?? 'all' ?>" 
                           class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        
                        <div class="page-info">
                            Page <?= $page ?> of <?= $totalPages ?>
                        </div>
                        
                        <a href="sales-report.php?filter=<?= $dateFilter ?>&page=<?= min($totalPages, $page + 1) ?>&product_type=<?= $productTypeFilter ?>&month=<?= $_GET['month'] ?? 'all' ?>&year=<?= $_GET['year'] ?? 'all' ?>" 
                           class="page-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            <?php } ?>
        </div>
    </main>

    <!-- Scripts -->
    <script>
        // Chart Data
        const salesData = <?php echo json_encode($sales); ?>;
        const categoryData = <?php echo json_encode($totalSalesData); ?>;
        
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        let salesChart;
        
        function createSalesChart(type = 'line') {
            if (salesChart) salesChart.destroy();
            
            const labels = salesData.map(item => new Date(item.payment_date).toLocaleDateString());
            const data = salesData.map(item => parseFloat(item.amount_paid));
            
            salesChart = new Chart(salesCtx, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Sales Amount',
                        data: data,
                        borderColor: 'rgba(99, 102, 241, 1)',
                        backgroundColor: type === 'line' ? 'rgba(99, 102, 241, 0.1)' : 'rgba(99, 102, 241, 0.8)',
                        fill: type === 'area',
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)'
                            }
                        },
                        y: {
                            grid: {
                                color: 'rgba(255, 255, 255, 0.1)'
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.7)',
                                callback: function(value) {
                                    return '₱' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        const categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.product_type),
                datasets: [{
                    data: categoryData.map(item => parseFloat(item.total_sales)),
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(139, 69, 19, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(14, 165, 233, 0.8)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: 'rgba(255, 255, 255, 0.9)',
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });
        
        // Initialize charts
        createSalesChart();
        
        // Chart type switcher
        document.querySelectorAll('.chart-type-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelector('.chart-type-btn.active').classList.remove('active');
                btn.classList.add('active');
                createSalesChart(btn.dataset.type);
            });
        });
        
        // Utility functions
        function handlePrint() {
            window.print();
        }
        
        function exportToCSV() {
            let csv = 'Product Type,Product Name,Quantity,Unit Price,Amount Paid,Payment Date\n';
            salesData.forEach(sale => {
                csv += `"${sale.product_type}","${sale.product_name}",${sale.quantity},"₱${parseFloat(sale.current_price).toFixed(2)}","₱${parseFloat(sale.amount_paid).toFixed(2)}","${sale.payment_date}"\n`;
            });
            
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sales-report.csv';
            a.click();
            window.URL.revokeObjectURL(url);
        }
        
        function toggleFullscreen() {
            const tableSection = document.querySelector('.table-section');
            tableSection.classList.toggle('fullscreen');
        }
        
        // Add loading animation to table rows
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.table-row');
            rows.forEach((row, index) => {
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #fff;
            overflow-x: hidden;
        }

        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 120px;
            height: 120px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape-4 {
            width: 100px;
            height: 100px;
            top: 40%;
            right: 30%;
            animation-delay: 1s;
        }

        .shape-5 {
            width: 140px;
            height: 140px;
            bottom: 10%;
            right: 20%;
            animation-delay: 3s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Header */
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .header-center {
            text-align: center;
            flex-grow: 1;
        }

        .main-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(45deg, #fff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 300;
        }

        .print-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(45deg, #10b981, #059669);
            border: none;
            border-radius: 50px;
            color: #fff;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }

        /* Main Content */
        .main-content {
            padding: 2rem 0;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }

        .total-sales .stat-icon { background: linear-gradient(45deg, #10b981, #059669); }
        .total-quantity .stat-icon { background: linear-gradient(45deg, #3b82f6, #1d4ed8); }
        .total-orders .stat-icon { background: linear-gradient(45deg, #f59e0b, #d97706); }
        .avg-order .stat-icon { background: linear-gradient(45deg, #8b5cf6, #7c3aed); }

        .stat-content h3 {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
        }

        .stat-trend {
            font-size: 0.85rem;
            font-weight: 500;
        }

        .stat-trend.positive { color: #10b981; }
        .stat-trend.negative { color: #ef4444; }

        /* Controls Panel */
        .controls-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 3rem;
        }

        .filter-section h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }

        .filter-group {
            margin-bottom: 2rem;
        }

        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        .btn-group {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .filter-btn:hover, .filter-btn.active {
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(99, 102, 241, 0.3);
        }

        .advanced-filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: flex-end;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-item label {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .filter-item select {
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: #fff;
            min-width: 150px;
        }

        .filter-item select option {
            background: #1f2937;
            color: #fff;
        }

        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .chart-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-header h3 {
            font-size: 1.3rem;
            color: #fff;
        }

        .chart-controls {
            display: flex;
            gap: 0.5rem;
        }

        .chart-type-btn {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .chart-type-btn.active, .chart-type-btn:hover {
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
        }

        .chart-wrapper {
            height: 300px;
            position: relative;
        }

        /* Table Section */
        .table-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 3rem;
            transition: all 0.3s ease;
        }

        .table-section.fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1000;
            border-radius: 0;
            overflow: auto;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header h3 {
            font-size: 1.3rem;
            color: #fff;
        }

        .table-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .table-container {
            overflow-x: auto;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.05);
        }

        .sales-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sales-table th {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sales-table td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.9);
        }

        .table-row {
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
        }

        .table-row:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .category-tag {
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .quantity-badge {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .price, .amount-paid {
            font-weight: 600;
            color: #10b981;
        }

        .date {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.3);
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.7);
        }

        /* Category Breakdown */
        .category-breakdown {
            margin-bottom: 3rem;
        }

        .category-breakdown h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #fff;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .category-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.6s ease forwards;
        }

        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .category-card:hover {
            transform: translateY(-5px);
        }

        .category-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }

        .category-info {
            flex-grow: 1;
        }

        .category-info h4 {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
            color: #fff;
        }

        .category-stats {
            display: flex;
            gap: 1rem;
        }

        .stat {
            display: flex;
            flex-direction: column;
        }

        .stat .label {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .stat .value {
            font-weight: 600;
            color: #fff;
        }

        .category-progress {
            width: 100px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(45deg, #10b981, #059669);
            border-radius: 2px;
            transition: width 1s ease;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            padding: 1rem 2rem;
        }

        .page-btn {
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 25px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-btn:hover:not(.disabled) {
            background: linear-gradient(45deg, #6366f1, #8b5cf6);
            transform: translateY(-2px);
        }

        .page-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-info {
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .main-title {
                font-size: 2rem;
            }

            .charts-section {
                grid-template-columns: 1fr;
            }

            .advanced-filters {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-item {
                width: 100%;
            }

            .table-container {
                font-size: 0.9rem;
            }

            .category-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Print Styles */
        @media print {
            .header, .controls-panel, .table-actions, .pagination-container {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .stat-card, .chart-container, .table-section, .category-card {
                background: white !important;
                border: 1px solid #ccc !important;
                color: black !important;
            }
        }
    </style>
</body>
</html>