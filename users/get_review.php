<?php
require_once('conn.php');

$cust_id = $_SESSION['customer']['cust_id'] ?? null;
if (!$cust_id) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['order_id'], $_GET['product_id'])) {
    $order_id = $_GET['order_id'];
    $product_id = $_GET['product_id'];
  
    $stmt = $pdo->prepare("SELECT review, rating FROM reviews WHERE order_id = :order_id AND product_id = :product_id AND customer_id = :customer_id");
    $stmt->execute([
      ':order_id' => $order_id,
      ':product_id' => $product_id,
      ':customer_id' => $cust_id
    ]);
  
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($review ? $review : []);
  }
exit;
?>
