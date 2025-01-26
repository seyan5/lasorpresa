<?php
include('conn.php');


$dateFilter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$productTypeFilter = isset($_GET['product_type']) ? $_GET['product_type'] : 'all';  // Added filter for product type
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
        $interval = "1"; // No filter, show all
}

$monthCondition = '';
$yearCondition = '';

// Check if month and year filters are set
if (isset($_GET['month']) && $_GET['month'] !== 'all') {
    $monthCondition = "AND MONTH(pay.created_at) = :month";
}

if (isset($_GET['year']) && $_GET['year'] !== 'all') {
    $yearCondition = "AND YEAR(pay.created_at) = :year";
}

// Added condition to filter by product type
if ($productTypeFilter !== 'all') {
    $productTypeCondition = " AND ec.ecat_id = :product_type";
} else {
    $productTypeCondition = "";
}

try {
    // Modify the query to account for the month, year, and product type filter
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
    
    // Bind parameters for month, year, and product type if set
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

    // Count total records to calculate pages
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM payment pay JOIN orders o ON pay.order_id = o.order_id JOIN order_items oi ON o.order_id = oi.order_id JOIN product p ON oi.product_id = p.p_id JOIN end_category ec ON p.ecat_id = ec.ecat_id WHERE $interval $productTypeCondition $monthCondition $yearCondition");
    
    // Bind the product type for the count query
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

    // Fetch total sales and total quantity for all products and product types
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

    // Fetch overall total sales and quantity
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

// Fetch product types for the filter dropdown
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Sales Report</h1>

         <!-- Back Button -->
         <div class="d-flex justify-content-start mb-3">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
        <div class="d-flex justify-content-end mb-3">
            <a href="sales-report.php?filter=daily" class="btn btn-primary mx-1">Daily</a>
            <a href="sales-report.php?filter=weekly" class="btn btn-primary mx-1">Weekly</a>
            <a href="sales-report.php?filter=monthly" class="btn btn-primary mx-1">Monthly</a>
            <a href="sales-report.php?filter=all" class="btn btn-secondary mx-1">All</a>
        </div>
        
        <!-- Filter by month, year, and product type -->
        <div class="d-flex justify-content-end mb-3">
            <form action="sales-report.php" method="GET">
                <input type="hidden" name="filter" value="<?= $dateFilter ?>">
                
                <!-- Month Dropdown -->
                <select name="month" class="form-select" onchange="this.form.submit()">
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
                
                <!-- Year Dropdown -->
                <select name="year" class="form-select" onchange="this.form.submit()">
                    <option value="all" <?= !isset($_GET['year']) || $_GET['year'] === 'all' ? 'selected' : '' ?>>All Years</option>
                    <?php
                    // Get the current year and create options for the last 5 years
                    $currentYear = date('Y');
                    for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                        echo '<option value="' . $i . '" ' . (isset($_GET['year']) && $_GET['year'] == $i ? 'selected' : '') . '>' . $i . '</option>';
                    }
                    ?>
                </select>
                
                <!-- Product Type Dropdown -->
                <select name="product_type" class="form-select" onchange="this.form.submit()">
                    <option value="all" <?= $productTypeFilter === 'all' ? 'selected' : '' ?>>All Product Types</option>
                    <?php foreach ($productTypes as $productType) { ?>
                        <option value="<?= $productType['ecat_id'] ?>" <?= $productTypeFilter == $productType['ecat_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($productType['ecat_name']) ?>
                        </option>
                    <?php } ?>
                </select>
            </form>
        </div>

        <?php if (empty($sales)) { ?>
            <!-- Display message if no products are found -->
            <div class="alert alert-warning text-center">
                <strong>No products found for this type.</strong>
            </div>
        <?php } else { ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Type</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Amount Paid</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale) { ?>
                        <tr>
                            <td><?= htmlspecialchars($sale['product_type']) ?></td>
                            <td><?= htmlspecialchars($sale['product_name']) ?></td>
                            <td><?= $sale['quantity'] ?></td>
                            <td>₱ <?= number_format($sale['current_price'], 2) ?></td>
                            <td>₱ <?= number_format($sale['amount_paid'], 2) ?></td>
                            <td><?= htmlspecialchars($sale['payment_date']) ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <!-- Display total sales for overall products -->
        <div class="card mt-4">
            <div class="card-header">
                <strong>Overall Sales</strong>
            </div>
            <div class="card-body">
                <p>Total Quantity Sold: <?= $overallTotalData['total_quantity'] ?></p>
                <p>Total Sales: ₱ <?= number_format($overallTotalData['total_sales'], 2) ?></p>
            </div>
        </div>

        <!-- Display total sales for each product type -->
        <div class="card mt-4">
            <div class="card-header">
                <strong>Sales by Product Type</strong>
            </div>
            <div class="card-body">
                <?php foreach ($totalSalesData as $data) { ?>
                    <p><strong><?= $data['product_type'] ?>:</strong> <?= $data['total_quantity'] ?> items sold, ₱ <?= number_format($data['total_sales'], 2) ?></p>
                <?php } ?>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between">
            <a href="sales-report.php?filter=<?= $dateFilter ?>&page=<?= max(1, $page - 1) ?>&product_type=<?= $productTypeFilter ?>&month=<?= isset($_GET['month']) ? $_GET['month'] : 'all' ?>&year=<?= isset($_GET['year']) ? $_GET['year'] : 'all' ?>" class="btn btn-secondary">Previous</a>
            <a href="sales-report.php?filter=<?= $dateFilter ?>&page=<?= min($totalPages, $page + 1) ?>&product_type=<?= $productTypeFilter ?>&month=<?= isset($_GET['month']) ? $_GET['month'] : 'all' ?>&year=<?= isset($_GET['year']) ? $_GET['year'] : 'all' ?>" class="btn btn-secondary">Next</a>
        </div>
    </div>
</body>
</html>
