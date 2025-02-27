<?php
require_once '../../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $data = json_decode(file_get_contents('php://input'), true);
  $orderID = $data['order_id'];
  $status = $data['status'];

  try {
    $stmt = $conn->prepare("UPDATE SupplierOrders SET Status = :status WHERE OrderID = :orderID");
    $stmt->execute([
      ':status' => $status,
      ':orderID' => $orderID
    ]);

    echo json_encode(['success' => true]);
  } catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
  }
} else {
  echo json_encode(['success' => false, 'error' => 'Invalid request']);
}