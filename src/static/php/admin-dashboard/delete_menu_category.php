<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: menu_categories.php");
  exit();
}

$categoryId = $_GET['id'];

// Delete category
$stmt = $conn->prepare("DELETE FROM MenuCategory WHERE CategoryID = :id");
$stmt->execute([':id' => $categoryId]);

header("Location: menu_categories.php");
exit();
?>