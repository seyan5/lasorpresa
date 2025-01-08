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
        $image = '../uploads/flower' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    } else {
        $image = ''; // Default or empty if no image
    }

    // Insert into the database
    $stmt = $pdo->prepare("INSERT INTO flowers (name, quantity, price, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $quantity, $price, $image]);

    // Redirect or display a success message
    header('Location: flowers.php');
    exit();
}
?>

<!-- Add Bootstrap and jQuery if not already included -->
<link rel="stylesheet" href="css/floweradd.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- Button to trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#flowerModal">
    Add Product
</button>

<!-- Modal Form -->
<div class="modal fade" id="flowerModal" tabindex="-1" role="dialog" aria-labelledby="flowerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="flowerModalLabel">Add New Flower</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="flower-add.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Flower Name:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" class="form-control" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                    </div>

                    <div class="form-group">
                        <label for="image">Flower Image:</label>
                        <input type="file" name="image" id="image" class="form-control">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Flower</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>