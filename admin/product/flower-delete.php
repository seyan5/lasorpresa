<?php
// Database connection
require_once('../header.php');

$id = $_GET['id'] ?? 0;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM flowers WHERE id = ?");
    $stmt->execute([$id]);
}

// Redirect to flower list page
header('Location: flowers.php');
exit();
?>
