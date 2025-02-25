<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
  echo json_encode(['error' => 'Invalid request']);
  exit;
}

$notificationID = intval($_POST['id']);
$currentStatus = isset($_POST['status']) ? intval($_POST['status']) : 0;
$newStatus = $currentStatus ? 0 : 1; // Toggle the status

try {
  // Update the notification status
  $query = "UPDATE Notification SET IsRead = ? WHERE NotificationID = ? AND UserID IS NULL";
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