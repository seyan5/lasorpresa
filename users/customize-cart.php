<?php
require("conn.php");

if (!isset($_SESSION['customization']) || empty($_SESSION['customization'])) {
    echo "No customization found. Redirecting...";
    header("refresh:3;url=customization.php");
    exit;
}

$customizations = $_SESSION['customization'];
?>
<?php include('navuser.php'); ?>
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="../css/customize-cart.css?">
<?php include('back.php'); ?>
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
    <img src="<?php echo htmlspecialchars(empty($expected_image) ? '/lasorpresa/images/default-image.jpg' : (strpos($expected_image, '/') === 0 ? $expected_image : 'uploads/' . $expected_image)); ?>" alt="Customization Preview">
</div>


<div class="container">
    <form action="customize-checkout-details.php" method="POST">
        <div class="cart">
            <h3>Your Floral Arrangement Customizations</h3>
            <?php foreach ($customizations as $index => $customization): ?>
                <div class="cart-item">
                    <input type="checkbox" name="selected_customizations[]" value="<?php echo $index; ?>" id="customization-<?php echo $index; ?>" 
                        onchange="updateTotalPrice()">
                    <label for="customization-<?php echo $index; ?>">
                        <h4>Customization #<?php echo $index + 1; ?></h4>
                    </label>
                    
                    <?php
                    $stmt = $pdo->prepare("SELECT container_name, price FROM container WHERE container_id = ?");
                    $stmt->execute([$customization['container_type']]);
                    $container = $stmt->fetch(PDO::FETCH_ASSOC);

                    $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = ?");
                    $stmt->execute([$customization['container_color']]);
                    $color = $stmt->fetch(PDO::FETCH_ASSOC);

                    $container_name = $container['container_name'] ?? "Unknown Container";
                    $container_price = $container['price'] ?? 0;
                    $color_name = $color['color_name'] ?? "Unknown Color";

                    $customization_total = $container_price;

                    foreach ($customization['flowers'] as $flower) {
                        $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = ?");
                        $stmt->execute([$flower['flower_type']]);
                        $flower_data = $stmt->fetch(PDO::FETCH_ASSOC);

                        $flower_price = $flower_data['price'] ?? 0;
                        $flower_quantity = $flower['num_flowers'] ?? 0;
                        $customization_total += $flower_price * $flower_quantity;
                    }
                    ?>

                    <p><strong>Container Type:</strong> <?php echo htmlspecialchars($container_name); ?> (₱<?php echo number_format($container_price, 2); ?>)</p>
                    <p><strong>Container Color:</strong> <?php echo htmlspecialchars($color_name); ?></p>

                    <h5>Flowers:</h5>
                    <ul>
                        <?php foreach ($customization['flowers'] as $flower): ?>
                            <?php
                            $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = ?");
                            $stmt->execute([$flower['flower_type']]);
                            $flower_data = $stmt->fetch(PDO::FETCH_ASSOC);

                            $flower_name = $flower_data['name'] ?? "Unknown Flower";
                            $flower_price = $flower_data['price'] ?? 0;
                            $flower_quantity = $flower['num_flowers'] ?? 0;
                            ?>
                            <li>
                                <p><strong>Flower:</strong> <?php echo htmlspecialchars($flower_name); ?> (₱<?php echo number_format($flower_price, 2); ?> each)</p>
                                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($flower_quantity); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <p><strong>Remarks:</strong> <?php echo htmlspecialchars($customization['remarks'] ?? 'None'); ?></p>
                    <div class="image-preview">
                        <img src="<?php echo htmlspecialchars(!empty($customization['expected_image']) ? 'uploads/' . $customization['expected_image'] : '/lasorpresa/images/default-image.jpg'); ?>" alt="Expected Output">
                    </div>
                    <p><strong>Subtotal:</strong> ₱<span class="subtotal" data-price="<?php echo $customization_total; ?>"><?php echo number_format($customization_total, 2); ?></span></p>
                    <hr>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="payment">
            <h3>Summary</h3>
            <p><strong>Total Price:</strong><span id="total-price">0.00</span></p>
            <button type="submit" class="checkout">Proceed To Checkout</button>
        </div>
    </form>
</div>

<script>
    function updateTotalPrice() {
        const checkboxes = document.querySelectorAll('input[name="selected_customizations[]"]:checked');
        let totalPrice = 0;

        checkboxes.forEach(checkbox => {
            const cartItem = checkbox.closest('.cart-item');
            const subtotalElement = cartItem.querySelector('.subtotal');
            const subtotal = parseFloat(subtotalElement.getAttribute('data-price'));
            totalPrice += subtotal;
        });

        document.getElementById('total-price').textContent = totalPrice.toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).replace('PHP', '');
    }
</script>
