<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';

if (!isset($_POST['id']) || !isset($_POST['status'])) {
  echo json_encode(['error' => 'Invalid request']);
  exit;
}

$notificationID = intval($_POST['id']);
$currentStatus = intval($_POST['status']);
$newStatus = $currentStatus ? 0 : 1;

try {
  $query = "UPDATE Notification SET IsRead = ? WHERE NotificationID = ?";
  $stmt = $conn->prepare($query);
  $stmt->execute([$newStatus, $notificationID]);

  if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'newStatus' => $newStatus]);
  } else {
    echo json_encode(['error' => 'No rows affected']);
  }
} catch (PDOException $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
?>