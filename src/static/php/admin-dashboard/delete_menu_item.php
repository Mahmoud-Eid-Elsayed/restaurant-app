<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: ../menu_items.php");
  exit();
}

$itemId = $_GET['id'];

// Delete item
$stmt = $conn->prepare("DELETE FROM MenuItem WHERE ItemID = :id");
$stmt->execute([':id' => $itemId]);

header("Location: menu_items.php");
exit();
?>