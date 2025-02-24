<?php
session_start();

require_once __DIR__ . '/../../connection/db.php';

if (!isset($_SESSION['user'])) {
  die("User not logged in.");
}

// Fetch customer ID from session
$customer_id = $_SESSION['user']['id'];

try {
  // Fetch notifications for the customer
  $query = "SELECT * FROM Notification WHERE UserID = ? OR UserID IS NULL ORDER BY Timestamp DESC";
  $stmt = $conn->prepare($query);
  $stmt->execute([$customer_id]);
  $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching notifications: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Notifications</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="container mt-5">
    <h2>Your Notifications 🔔</h2>
    <div class="list-group">
      <?php if (empty($notifications)): ?>
        <p>No new notifications.</p>
      <?php else: ?>
        <?php foreach ($notifications as $notification): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <h5 class="mb-1"><?php echo htmlspecialchars($notification['Message']); ?></h5>
              <small><?php echo $notification['Timestamp']; ?></small>
            </div>
            <button class="btn btn-sm <?php echo $notification['IsRead'] ? 'btn-success' : 'btn-warning'; ?>"
              onclick="toggleReadStatus(<?php echo $notification['NotificationID']; ?>, <?php echo $notification['IsRead']; ?>)">
              <?php echo $notification['IsRead'] ? 'Read ✅' : 'Mark as Read 🔴'; ?>
            </button>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
  <script>
    function toggleReadStatus(notificationID, currentStatus) {
      fetch('toggle_notification.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + notificationID + '&status=' + currentStatus
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            location.reload();
          } else {
            alert('Error updating notification');
          }
        })
        .catch(error => console.error('Error:', error));
    }
  </script>

</body>

</html>