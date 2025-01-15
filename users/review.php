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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Reviews</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"> <!-- Font Awesome for stars -->
    <link rel="stylesheet" href="../css/navhead.css">
    <style>
        .box { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
        .stars { color: gold; }
        .user { display: flex; align-items: center; margin-top: 10px; }
        .user img { width: 60px; height: 60px; border-radius: 50%; margin-right: 10px; }
        .user-info { flex-grow: 1; }
        .fas { font-size: 16px; }

        /* Image styling */
        .box img { 
            max-width: 100%; /* Ensures the image does not exceed the container's width */
            height: auto; /* Keeps the image aspect ratio intact */
            max-height: 200px; /* Limits the height of the image */
            object-fit: contain; /* Ensures the image fits within the box without distortion */
            margin-top: 10px; /* Adds a little space between the image and text */
        }

        .review-date { font-size: 14px; color: #888; margin-top: 5px; }
    </style>
</head>



<body>
    <h1>Customer Reviews</h1>

    <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
            <div class="box">
                <!-- Displaying Rating Stars -->
                <div class="stars">
                    <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                    <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                        <i class="far fa-star"></i>
                    <?php endfor; ?>
                </div>

                <!-- Review Text -->
                <p><?php echo nl2br(htmlspecialchars($review['review'])); ?></p>
                
                <!-- Product Image -->
                <img src="../admin/uploads/<?php echo htmlspecialchars($review['featured_photo']); ?>" alt="<?php echo htmlspecialchars($review['product_name']); ?>">

                <!-- User Information -->
                <div class="user">
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($review['cust_name']); ?></h3>
                        <span>Reviewed Product: <?php echo htmlspecialchars($review['product_name']); ?></span>
                    </div>
                    <span class="fas fa-quote-right"></span>
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
</body>
</html>
