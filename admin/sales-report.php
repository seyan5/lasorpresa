<?php
ob_start();
session_start();
include("inc/config.php");
include("inc/functions.php");
include("inc/CSRF_Protect.php");

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

// Added condition to filter by product type
if ($productTypeFilter !== 'all') {
    $productTypeCondition = " AND ec.ecat_id = :product_type";
} else {
    $productTypeCondition = "";
}

try {
    // Modify the query to account for the product type filter
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
        WHERE $interval $productTypeCondition
        ORDER BY pay.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
    // Bind the product type if it's set
    if ($productTypeFilter !== 'all') {
        $stmt->bindParam(':product_type', $productTypeFilter, PDO::PARAM_INT);
    }

    $stmt->execute();
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total records to calculate pages
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM payment pay JOIN orders o ON pay.order_id = o.order_id JOIN order_items oi ON o.order_id = oi.order_id JOIN product p ON oi.product_id = p.p_id JOIN end_category ec ON p.ecat_id = ec.ecat_id WHERE $interval $productTypeCondition");
    
    // Bind the product type for the count query
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
        WHERE $interval
        GROUP BY ec.ecat_id
    ");
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
        WHERE $interval
    ");
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
        
        <div class="d-flex justify-content-end mb-3">
            <a href="sales-report.php?filter=daily" class="btn btn-primary mx-1">Daily</a>
            <a href="sales-report.php?filter=weekly" class="btn btn-primary mx-1">Weekly</a>
            <a href="sales-report.php?filter=monthly" class="btn btn-primary mx-1">Monthly</a>
            <a href="sales-report.php?filter=all" class="btn btn-secondary mx-1">All</a>
        </div>
        
        <!-- Filter by product type -->
        <div class="d-flex justify-content-end mb-3">
            <form action="sales-report.php" method="GET">
                <input type="hidden" name="filter" value="<?= $dateFilter ?>">
                <select name="product_type" class="form-select" onchange="this.form.submit()">
                    <option value="all" <?= $productTypeFilter === 'all' ? 'selected' : '' ?>>All Product Types</option>
                    <?php foreach ($productTypes as $type) { ?>
                        <option value="<?= $type['ecat_id'] ?>" <?= $productTypeFilter == $type['ecat_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['ecat_name']) ?>
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
                            <td>P<?= number_format($sale['current_price'], 2) ?></td>
                            <td>P<?= number_format($sale['amount_paid'], 2) ?></td>
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
                <p>Total Sales: P<?= number_format($overallTotalData['total_sales'], 2) ?></p>
            </div>
        </div>

        <!-- Display total sales for each product type -->
        <div class="card mt-4">
            <div class="card-header">
                <strong>Sales by Product Type</strong>
            </div>
            <div class="card-body">
                <?php foreach ($totalSalesData as $data) { ?>
                    <p><?= htmlspecialchars($data['product_type']) ?>: </p>
                    <ul>
                        <li>Total Quantity Sold: <?= $data['total_quantity'] ?></li>
                        <li>Total Sales: P<?= number_format($data['total_sales'], 2) ?></li>
                    </ul>
                <?php } ?>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="sales-report.php?filter=<?= $dateFilter ?>&page=<?= max(1, $page - 1) ?>&product_type=<?= $productTypeFilter ?>" class="btn btn-secondary">Previous</a>
            <a href="sales-report.php?filter=<?= $dateFilter ?>&page=<?= min($totalPages, $page + 1) ?>&product_type=<?= $productTypeFilter ?>" class="btn btn-secondary">Next</a>
        </div>
    </div>
</body>
</html>
