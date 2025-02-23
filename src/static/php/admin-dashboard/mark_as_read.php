<?php
session_start();

// Include the database connection
require_once __DIR__ . '/../../connection/db.php';

// Check if the notification ID is provided
if (!isset($_POST['id'])) {
  die("Notification ID not provided.");
}
$notificationID = $_POST['id'];

try {
  // Mark the notification as read
  $query = "UPDATE Notification SET IsRead = TRUE WHERE NotificationID = ?";
  $stmt = $conn->prepare($query);
  $stmt->execute([$notificationID]);

  // Redirect back to the notifications page
  header("Location: admin_notifications.php");
  exit();
} catch (PDOException $e) {
  die("Error marking notification as read: " . $e->getMessage());
}
?>