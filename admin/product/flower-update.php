<?php
// Database connection
require_once('../header.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Image upload logic (optional)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image = 'uploads/flower' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    } else {
        // Keep existing image if no new one is uploaded
        $stmt = $pdo->prepare("SELECT image FROM flowers WHERE id = ?");
        $stmt->execute([$id]);
        $flower = $stmt->fetch(PDO::FETCH_ASSOC);
        $image = $flower['image'];
    }

    // Update the database
    $stmt = $pdo->prepare("UPDATE flowers SET name = ?,  price = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $price, $image, $id]);

    // Redirect or display a success message
    header('Location: flower.php');
    exit();
}
?>
