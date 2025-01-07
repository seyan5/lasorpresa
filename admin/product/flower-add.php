<?php
// Database connection
require_once('../header.php'); // Make sure your connection is set

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];

    // Image upload logic (optional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = 'uploads/flower' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    } else {
        $image = ''; // Default or empty if no image
    }

    // Insert into the database
    $stmt = $pdo->prepare("INSERT INTO flowers (name, quantity, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $quantity, $price, $image]);

    // Redirect or display a success message
    header('Location: flower.php');
    exit();
}
?>



<form action="flower-add.php" method="POST" enctype="multipart/form-data">
    <label for="name">Flower Name:</label>
    <input type="text" name="name" id="name" required>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" step="0.01" required>

    <label for="price">Price:</label>
    <input type="number" name="price" id="price" step="0.01" required>

    <label for="image">Flower Image:</label>
    <input type="file" name="image" id="image">

    <button type="submit">Add Flower</button>
</form>
