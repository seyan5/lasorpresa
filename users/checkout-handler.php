<?php
session_start();
require 'header.php'; // Include any common setup, such as database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form inputs
    $full_name = htmlspecialchars($_POST['full_name']);
    $address = htmlspecialchars($_POST['address']);
    $city = htmlspecialchars($_POST['city']);
    $postal_code = htmlspecialchars($_POST['postal_code']);
    $phone = htmlspecialchars($_POST['phone']);

    if (empty($full_name) || empty($address) || empty($city) || empty($postal_code) || empty($phone)) {
        echo "All fields are required!";
        exit;
    }

    // Process the order
    $order_total = array_sum(array_map(function ($item) {
        return $item['price'] * $item['quantity'];
    }, $_SESSION['cart']));

    // Save order to database (example, adjust for your DB structure)
    $statement = $pdo->prepare("
        INSERT INTO orders (customer_id, full_name, address, city, postal_code, phone, total, created_at)
        VALUES (:customer_id, :full_name, :address, :city, :postal_code, :phone, :total, NOW())
    ");
    $statement->execute([
        ':customer_id' => $_SESSION['customer']['cust_id'] ?? null, // Null if guest checkout
        ':full_name' => $full_name,
        ':address' => $address,
        ':city' => $city,
        ':postal_code' => $postal_code,
        ':phone' => $phone,
        ':total' => $order_total
    ]);

    // Retrieve the order ID
    $order_id = $pdo->lastInsertId();

    // Save order items
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $statement = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (:order_id, :product_id, :quantity, :price)
        ");
        $statement->execute([
            ':order_id' => $order_id,
            ':product_id' => $product_id,
            ':quantity' => $item['quantity'],
            ':price' => $item['price']
        ]);
    }

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to confirmation page
    header('Location: order-confirmation.php?order_id=' . $order_id);
    exit;
} else {
    echo "Invalid request method!";
    exit;
}
?>
