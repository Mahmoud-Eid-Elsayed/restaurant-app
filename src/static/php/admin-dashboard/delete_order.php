<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: orders.php");
  exit();
}

$orderId = $_GET['id'];


$stmt = $conn->prepare("DELETE FROM `Order` WHERE OrderID = :id");
$stmt->execute([':id' => $orderId]);

header("Location: orders.php");
exit();
?>