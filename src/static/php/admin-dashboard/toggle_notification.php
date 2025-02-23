<?php
error_reporting(E_ALL); // Report all PHP errors
ini_set('display_errors', 1); // Display errors to the browser

require_once '../../static/connection/db.php'; // Adjust path if necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
  $notification_id = intval($_POST['notification_id']);

  try {
    // Fetch the current status of the notification
    $query = "SELECT IsRead FROM Notification WHERE NotificationID = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$notification_id]);
    $row = $stmt->fetch();

    if ($row) {
      $new_status = $row['IsRead'] ? 0 : 1; // Toggle status

      // Update the notification status
      $update_query = "UPDATE Notification SET IsRead = ? WHERE NotificationID = ?";
      $update_stmt = $conn->prepare($update_query);
      $update_stmt->execute([$new_status, $notification_id]);

      // Return a JSON response
      echo json_encode(["success" => true, "new_status" => $new_status]);
    } else {
      echo json_encode(["success" => false, "error" => "Notification not found"]);
    }
  } catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => "Database error: " . $e->getMessage()]);
  }
} else {
  echo json_encode(["success" => false, "error" => "Invalid request"]);
}
?>