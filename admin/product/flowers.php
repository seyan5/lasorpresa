<?php
// Database connection
require_once('../header.php');

$stmt = $pdo->prepare("SELECT * FROM flowers");
$stmt->execute();
$flowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Flower List</h1>
<a href="flower-add.php">Add New Flower</a>
<ul>
    <?php foreach ($flowers as $flower): ?>
        <li>
            <img src="<?php echo htmlspecialchars($flower['image']); ?>" alt="<?php echo htmlspecialchars($flower['name']); ?>" width="50">
            <strong><?php echo htmlspecialchars($flower['name']); ?></strong><br>
            $<?php echo number_format($flower['price'], 2); ?><br>
            <a href="flower-edit.php?id=<?php echo $flower['id']; ?>">Edit</a>
            <a href="flower-delete.php?id=<?php echo $flower['id']; ?>">Delete</a>
        </li>
    <?php endforeach; ?>
</ul>
