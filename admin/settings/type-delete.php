<?php
require_once('../header.php');

if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
    header('location: ../logout.php');
    exit;
}

$id = intval($_REQUEST['id']);

$statement = $pdo->prepare("SELECT * FROM type WHERE type_id = ?");
$statement->execute([$id]);
if ($statement->rowCount() == 0) {
    header('location: ../logout.php');
    exit;
}

$deleteStatement = $pdo->prepare("DELETE FROM type WHERE type_id = ?");
$deleteStatement->execute([$id]);

header('location: type.php');
exit;
?>