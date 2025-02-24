<?php
session_start();

require_once __DIR__ . '/../../connection/db.php';

if (!isset($_GET['id'])) {
  die("Notification ID not provided.");
}
$notificationID = $_GET['id'];

try {
  // Mark the notification as read
  $query = "UPDATE Notification SET IsRead = TRUE WHERE NotificationID = ? AND UserID = ?";
  $stmt = $conn->prepare($query);
  $stmt->execute([$notificationID, $_SESSION['user']['id']]);


  // Fetch the notification details
  $query = "SELECT * FROM Notification WHERE NotificationID = ?";
  $stmt = $conn->prepare($query);
  $stmt->execute([$notificationID]);
  $notification = $stmt->fetch();

  if (!$notification) {
    die("Notification not found.");
  }
} catch (PDOException $e) {
  die("Error fetching notification: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Notification</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h2>Notification Details</h2>
    <div class="card">
      <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($notification['Message']); ?></h5>
        <p class="card-text">
          <strong>Type:</strong> <?php echo htmlspecialchars($notification['NotificationType']); ?><br>
          <strong>Timestamp:</strong> <?php echo $notification['Timestamp']; ?><br>
          <strong>Status:</strong> <?php echo $notification['IsRead'] ? 'Read' : 'Unread'; ?>
        </p>
        <a href="customer_notifications.php" class="btn btn-primary">Back to Notifications</a>
      </div>
    </div>
  </div>
</body>

</html>