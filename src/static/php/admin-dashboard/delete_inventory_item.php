<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: inventory.php");
  exit();
}

$itemId = $_GET['id'];


$stmt = $conn->prepare("DELETE FROM InventoryItem WHERE InventoryItemID = :id");
$stmt->execute([':id' => $itemId]);

header("Location: inventory.php");
exit();
?>