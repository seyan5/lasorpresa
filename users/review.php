<?php
// Include database configuration
include("conn.php");

// Fetch all reviews from the reviews table
try {
    $stmt = $pdo->prepare("SELECT r.review_id, r.review, r.rating, r.created_at, p.p_id, p.name AS product_name, p.featured_photo, c.cust_name 
                           FROM reviews r
                           JOIN product p ON r.product_id = p.p_id
                           JOIN customer c ON r.customer_id = c.cust_id
                           ORDER BY r.created_at DESC");
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching reviews: " . $e->getMessage();
    exit;
}
?>
<?php include('navuser.php'); ?>

<link rel="stylesheet" href="../css/review.css">

<body>
<h1>Customer Reviews</h1>
<div class="container">
    <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="box">
                <div class="user">
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($review['cust_name']); ?></h3>
                        <span>Reviewed Product: <?php echo htmlspecialchars($review['product_name']); ?></span>
                    </div>
                </div>
                

                <!-- Review Text -->
                <h>comment: </h>
                <p><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                
                <!-- Product Image -->
                <img src="../admin/uploads/<?php echo htmlspecialchars($review['featured_photo']); ?>" alt="<?php echo htmlspecialchars($review['product_name']); ?>">

                <!-- Displaying Rating Stars -->
                <div class="stars">
                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                    <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                        <i class="far fa-star"></i>
                    <?php endfor; ?>
                </div>

                <!-- Review Date -->
                <div class="review-date">
                    <strong>Reviewed on:</strong> <?php echo date("F j, Y, g:i a", strtotime($review['created_at'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No reviews available.</p>
    <?php endif; ?>
</div>
<?php include('../loading.php'); ?>

