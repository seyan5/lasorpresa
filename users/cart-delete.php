<?php
require 'header.php';


// Check if the cart exists and the item index is provided
if (isset($_SESSION['cart']) && isset($_POST['item_index'])) {
    $itemIndex = $_POST['item_index'];

    // Remove the item from the cart array
    array_splice($_SESSION['cart'], $itemIndex, 1);
    
    // Optionally, you can redirect back to the cart page to see the updated cart
    header('Location: shopcart.php');
    exit;
}
?>
