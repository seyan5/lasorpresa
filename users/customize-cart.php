<?php
require("conn.php");
if (!isset($_SESSION['customization'])) {
    echo "No customization found. Please go back and customize your arrangement.";
    exit;
}

$customization = $_SESSION['customization'];
$grouped_customization = [];
$total_price = 0;

foreach ($customization as $item) {
    $key = $item['container_type'] . '-' . $item['container_color'] . '-' . $item['remarks'];

    if (!isset($grouped_customization[$key])) {
        $stmt = $pdo->prepare("SELECT price FROM container WHERE container_id = :container_id");
        $stmt->execute(['container_id' => $item['container_type']]);
        $container = $stmt->fetch(PDO::FETCH_ASSOC);
        $container_price = $container['price'] ?? 0;

        $grouped_customization[$key] = [
            'container_type' => $item['container_type'],
            'container_color' => $item['container_color'],
            'remarks' => $item['remarks'] ?? 'No remarks provided',
            'flowers' => [],
            'container_price' => $container_price,
            'expected_image' => $item['expected_image'] ?? "../images/previews/default.jpg",
        ];

        $total_price += $container_price;
    }

    $grouped_customization[$key]['flowers'][] = $item;

    $stmt = $pdo->prepare("SELECT price FROM flowers WHERE id = :flower_id");
    $stmt->execute(['flower_id' => $item['flower_type']]);
    $flower = $stmt->fetch(PDO::FETCH_ASSOC);
    $flower_price = $flower['price'] ?? 0;

    $total_price += $flower_price * $item['num_flowers'];
}

$_SESSION['total_price'] = $total_price;
?>
<?php include('navuser.php'); ?>
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="../css/customize-cart.css">
<div class="container">
  <div class="cart">
    <h3>Your Floral Arrangement Customization</h3>
    <?php foreach ($grouped_customization as $key => $group): ?>
      <?php
      $stmt = $pdo->prepare("SELECT container_name FROM container WHERE container_id = :container_id");
      $stmt->execute(['container_id' => $group['container_type']]);
      $container = $stmt->fetch(PDO::FETCH_ASSOC);
      $container_name = $container['container_name'] ?? "Unknown Container";

      $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = :color_id");
      $stmt->execute(['color_id' => $group['container_color']]);
      $color = $stmt->fetch(PDO::FETCH_ASSOC);
      $color_name = $color['color_name'] ?? "Unknown Color";

      $expected_image = $group['expected_image'];
      ?>

      <div class="cart-item">
        <img src="<?php echo htmlspecialchars("uploads/" . $expected_image); ?>" alt="Customization Preview">
      </div>
      <p><strong>Remarks:</strong> <?php echo htmlspecialchars($group['remarks']); ?></p>
    <?php endforeach; ?>
  </div>

  <div class="payment">
    <h3>Summary</h3>
    <div class="summary">
    <p><strong>Container Type:</strong> <?php echo htmlspecialchars($container_name); ?> ($<?php echo number_format($group['container_price'], 2); ?>)</p>
          <p><strong>Container Color:</strong> <?php echo htmlspecialchars($color_name); ?></p>
          <?php foreach ($group['flowers'] as $flower_item): ?>
              <?php
              $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = :flower_id");
              $stmt->execute(['flower_id' => $flower_item['flower_type']]);
              $flower = $stmt->fetch(PDO::FETCH_ASSOC);
              $flower_name = $flower['name'] ?? "Unknown Flower";
              $flower_price = $flower['price'] ?? 0;
              $flower_total_price = $flower_price * $flower_item['num_flowers'];
              ?>
              <p><strong>Flower: </strong><?php echo htmlspecialchars($flower_name); ?> ($<?php echo number_format($flower_price, 2); ?> per flower)</p>
             <p><strong>Quantity: </strong><?php echo htmlspecialchars($flower_item['num_flowers']); ?></p>
                <strong><p>Total:</strong> $<?php echo number_format($flower_total_price, 2); ?></p>
            <?php endforeach; ?>
      <p><strong>Total Price: </strong><span>₱<?php echo number_format($total_price, 2); ?></span></p>
    </div>
    <button class="checkout" onclick="proceedToCheckout()">Checkout &gt;</button> 
  </div>
</div>
<script>
    function proceedToCheckout() {
        window.location.href = "customize-checkout.php";
    }
</script>

</body>
</html>
