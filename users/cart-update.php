<?php
session_start();

if (isset($_POST['item_index']) && isset($_POST['action'])) {
    $index = (int)$_POST['item_index'];
    $action = $_POST['action'];

    if (isset($_SESSION['cart'][$index])) {
        if ($action === 'increase') {
            $_SESSION['cart'][$index]['quantity']++;
        } elseif ($action === 'decrease' && $_SESSION['cart'][$index]['quantity'] > 1) {
            $_SESSION['cart'][$index]['quantity']--;
        }
    }
}

// Redirect back to the cart page
header('Location: shopcart.php');
exit;
?>
