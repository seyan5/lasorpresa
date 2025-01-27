<?php
require("conn.php");

if (!isset($_SESSION['customization']) || empty($_SESSION['customization'])) {
    echo "No customization found. Redirecting...";
    header("refresh:2;url=customization.php");
    exit;
}

$customizations = $_SESSION['customization'];
?>

<?php

// Check if the remove request is sent
if (isset($_POST['remove_customization']) && isset($_POST['index'])) {
  $index = (int)$_POST['index'];

  // Remove the customization from the session based on the index
  if (isset($_SESSION['customization'][$index])) {
      unset($_SESSION['customization'][$index]);

      // Reindex the session array to prevent any gaps in the indices
      $_SESSION['customization'] = array_values($_SESSION['customization']);
  }

  // Send a response back indicating success
  echo json_encode(['status' => 'success']);
  exit;
}

if (!isset($_SESSION['customization']) || empty($_SESSION['customization'])) {
  echo "No customization found. Redirecting...";
  header("refresh:3;url=customization.php");
  exit;
}

$customizations = $_SESSION['customization'];


if (!isset($_SESSION['customization']) || empty($_SESSION['customization'])) {
    echo "No customization found. Redirecting...";
    header("refresh:3;url=customization.php");
    exit;
}

$customizations = $_SESSION['customization'];
?>


<?php include('navuser.php'); ?>
<?php include('back.php'); ?>
<style>
  :root{
    --pink: #e84393;
    --main: #d0bcb3;
    --font: #d18276;
    --button: #d6a98f;
  }
  .container {
    display: flex;
    max-width: 1600px;
    margin: 40px auto;
    gap: 30px;
    margin-top: 25rem;
    flex-direction: row;  /* Ensure items are aligned horizontally */
  }

  .cart .price{
    margin-left: 35rem;
  }

  .cart, .payment {
    border-radius: 12px;
    padding: 30px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .cart {
    flex: 3;
    margin-top: -5rem;
    max-height: 50vh; /* Set a max height to limit the cart size */
    overflow-y: auto; /* Enable vertical scrolling */
    padding-right: 2rem;
    scrollbar-width: thin; /* Slim scrollbar */
    scrollbar-color: #ccc transparent; /* Custom scrollbar color */
}


  .cart::-webkit-scrollbar {
    width: 8px;
  }

  .cart::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 4px;
  }

  .cart::-webkit-scrollbar-thumb:hover {
    background: #aaa;
  }

  .cart::-webkit-scrollbar-track {
    background: transparent;
  }

  .cart p {
    font-size: 15px;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
    font-style: italic;
  }

  .payment {
    width: 400px;
    max-width: 100%;
    background-color: rgb(233, 221, 204);
    align-self: flex-start;
    position: sticky;
    top: 20px;
  }

  .cart h3, .payment h3 {
    font-size: 25px;
    color: #333;
  }

  .cart-item {
    display: flex;
    align-items: center;
    margin-top: 20px;
    border-bottom: 2px solid #ddd;
    padding-bottom: 15px;
  }

  .cart-item img {
  width: 200px; /* Adjusted to a smaller size */
  height: 200px; /* Adjusted to match the width and keep aspect ratio */
  object-fit: contain; /* Ensures the entire image is visible without distortion */
  border-radius: 10px;
}

  .cart-item div {
    margin-left: 15px;
    flex: 1;
  }

  .cart-item h4 {
    margin: 0;
    font-size: 15px;
  }

  .cart-item p {
    margin: 8px 0 0;
    font-size: 20px;
    color: #666;
  }

  .delete {
    background: none;
    border: none;
    cursor: pointer;
    color: #aaa;
    font-size: 25px;
    padding: 8px;
    border-radius: 4px;
    transition: color 0.3s ease, background-color 0.3s ease;
  }

  .delete:hover {
    color: #ff0000;
  }

  .summary {
  margin-top: 30px;
  max-height: 300px; /* Set a fixed height for the scrollable area */
  overflow-y: auto; /* Make the content scrollable */
}

.summary p {
  display: flex;
  justify-content: space-between;
  margin: 15px 0;
  font-size: 16px;
  font-weight: bold;
  border-bottom: 1px solid #ccc; /* Add a line between orders */
  padding-bottom: 10px; /* Add some space between the text and the line */
}

.checkout {
  width: 100%;
  background-color: #333;
  color: #fff;
  padding: 15px;
  font-size: 18px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  position: sticky;
  bottom: 0; /* Fix the button at the bottom of the container */
  z-index: 10; /* Ensure the button stays on top of content */
}

  .checkout:hover {
    background-color: var(--button);
  }
</style>

<div class="container"> 
    <form action="customize-checkout-details.php" method="POST">
        <div class="cart">
        <hr>
        <h3>Your Floral Arrangement Customizations</h3>
        <?php foreach ($customizations as $index => $customization): ?>
          <div class="cart-item">
    <input type="checkbox" name="selected_customizations[]" value="<?php echo $index; ?>" id="customization-<?php echo $index; ?>" 
           onchange="updateTotalPrice()">
    <label for="customization-<?php echo $index; ?>">
        <h4><?php echo $index + 1; ?></h4>
    </label>
    <img src="<?php echo htmlspecialchars(!empty($customization['expected_image']) ? 'uploads/' . $customization['expected_image'] : '/lasorpresa/images/default-image.jpg'); ?>" 
         alt="<?php echo htmlspecialchars($item['name']); ?>" 
         width="50">
    
    <?php
    $stmt = $pdo->prepare("SELECT container_name, price FROM container WHERE container_id = ?");
    $stmt->execute([$customization['container_type']]);
    $container = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT color_name FROM color WHERE color_id = ?");
    $stmt->execute([$customization['container_color']]);
    $color = $stmt->fetch(PDO::FETCH_ASSOC);

    $container_name = $container['container_name'] ?? "Unknown Container";
    $container_price = $container['price'] ?? 0;
    $flower_price = $container['price'] ?? 0;  // This is used as a default value for now
    $color_name = $color['color_name'] ?? "Unknown Color";

    $customization_total = $container_price;  // Start with the container price

    // Loop through each flower and add its price to the total
    ?>
    
    <div>
        <p class="container-type">Container Type: <?php echo htmlspecialchars($container_name); ?> (₱<?php echo number_format($container_price, 2); ?>)</p>
        <p class="container-color">Container Color: <?php echo htmlspecialchars($color_name); ?></p> 
        <?php foreach ($customization['flowers'] as $flower): ?>
            <?php
            $stmt = $pdo->prepare("SELECT name, price FROM flowers WHERE id = ?");
            $stmt->execute([$flower['flower_type']]);
            $flower_data = $stmt->fetch(PDO::FETCH_ASSOC);

            $flower_name = $flower_data['name'] ?? "Unknown Flower";
            $flower_price = $flower_data['price'] ?? 0;
            $flower_quantity = $flower['num_flowers'] ?? 0;

            // Add flower price to customization total (flower price * quantity)
            $customization_total += $flower_price * $flower_quantity;
            ?>
            <p class="flower-details">Flower: <?php echo htmlspecialchars($flower_name); ?> (₱<?php echo number_format($flower_price, 2); ?> each)</p>
            <p class="flower-details">Quantity: <?php echo htmlspecialchars($flower_quantity); ?></p>
        <?php endforeach; ?>
        <p>Remarks: <?php echo htmlspecialchars($customization['remarks'] ?? 'None'); ?></p>
    </div>
    <div class="price">
        <p>Subtotal: ₱<span class="subtotal" data-price="<?php echo $customization_total; ?>"><?php echo number_format($customization_total, 2); ?></span></p>
        
    </div>
    <button type="button" class="remove-customization" onclick="removeCustomization(<?php echo $index; ?>)">Remove</button>
</div>
<?php endforeach; ?>

        </div>
    </form>
    <div class="payment">
    <h3>Summary</h3>
    <hr>
    <div class="summary">            
        <div id="summary-details"></div> <!-- This is where we'll display the customization details -->
        <p>Total Price: <span id="total-price">0.00</span></p>
    </div>
    <button type="submit" class="checkout">Proceed To Checkout</button>
</div>
</div>
                            
        
<script>
    function updateTotalPrice() {
    const checkboxes = document.querySelectorAll('input[name="selected_customizations[]"]:checked');
    let totalPrice = 0;
    let summaryDetails = '';

    checkboxes.forEach(checkbox => {
        const cartItem = checkbox.closest('.cart-item');
        const subtotalElement = cartItem.querySelector('.subtotal');
        const subtotal = parseFloat(subtotalElement.getAttribute('data-price'));
        totalPrice += subtotal;

        // Extract customization details
        const containerType = cartItem.querySelector('.container-type').textContent;
        const containerColor = cartItem.querySelector('.container-color').textContent;
        const flowers = cartItem.querySelectorAll('.flower-details');

        // Prepare flower details
        let flowerDetails = '';
        flowers.forEach(flower => {
            flowerDetails += `<p>${flower.textContent}</p>`;
        });

        // Append customization details to the summary
        summaryDetails += `
            <h4>Customization ${checkbox.value}</h4>
            <p>Container Type: ${containerType}</p>
            <p>Container Color: ${containerColor}</p>
            <div>${flowerDetails}</div>
            <hr>
        `;
    });

    // Update the summary details section and total price
    document.getElementById('total-price').textContent = totalPrice.toLocaleString('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).replace('PHP', '');
    
    document.getElementById('summary-details').innerHTML = summaryDetails;
}

document.querySelector('.checkout').addEventListener('click', function (event) {
    event.preventDefault();  // Prevent the default form submission (if needed)
    document.querySelector('form').submit();  // Submit the form manually
});

function removeCustomization(index) {
    // Find the checkbox associated with the customization to be removed
    const checkbox = document.getElementById(`customization-${index}`);
    
    // Uncheck the checkbox if it's selected
    if (checkbox.checked) {
        checkbox.checked = false;
        updateTotalPrice();  // Recalculate the total price
    }

    // Find the cart item to remove
    const cartItem = checkbox.closest('.cart-item');
    if (cartItem) {
        cartItem.remove();  // Remove the cart item element
    }

    // Send an AJAX request to the server to remove the customization from the session
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '', true);  // Send the request to the current page
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    // Send the index of the customization to remove
    xhr.send('remove_customization=true&index=' + encodeURIComponent(index));

    // After the request is complete, handle the response
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'success') {
                // Show the alert and redirect
                alert('Customization removed successfully!');
                window.location.href = 'customization.php';  // Redirect to customization.php
            }
        } else {
            console.error('Failed to remove customization');
        }
    };
}



</script>
