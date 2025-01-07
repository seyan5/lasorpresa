<?php
// Database connection
require_once('../header.php');

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM flowers WHERE id = ?");
$stmt->execute([$id]);
$flower = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h1>Edit Flower</h1>
<form action="flower-update.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $flower['id']; ?>">

    <label for="name">Flower Name:</label>
    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($flower['name']); ?>" required>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" id="quantity" value="<?php echo $flower['quantity']; ?>" step="0.01" required>

    <label for="price">Price:</label>
    <input type="number" name="price" id="price" value="<?php echo $flower['price']; ?>" step="0.01" required>

    <label for="image">Flower Image:</label>
    <input type="file" name="image" id="image">
    <img src="<?php echo $flower['image']; ?>" width="100">

    <button type="submit" onclick="return confirmUpdate()">Update Flower</button>

</form>

<script>
function confirmUpdate() {
    return confirm("Are you sure you want to update this flower?");
}
</script>