<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'], $_POST['quantity'])) {
    $index = intval($_POST['index']);
    $quantity = intval($_POST['quantity']);

    if (isset($_SESSION['cart'][$index])) {
        $_SESSION['cart'][$index]['quantity'] = $quantity;
        $newPrice = $_SESSION['cart'][$index]['price'] * $quantity;
        echo json_encode(['status' => 'success', 'newPrice' => $newPrice]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Item not found in cart.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
