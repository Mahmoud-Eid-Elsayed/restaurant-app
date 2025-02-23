<?php
session_start();

// Include the database connection
require_once __DIR__ . '/../../connection/db.php';

// Debugging: Check if the session is set
if (!isset($_SESSION['user'])) {
  die("User not logged in.");
}

// Fetch customer ID from session
$customer_id = $_SESSION['user']['id'];

try {
  // Fetch notifications for the customer
  $query = "SELECT * FROM Notification WHERE UserID = ? ORDER BY Timestamp DESC";
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
          <div class="list-group-item">
            <div class="d-flex w-100 justify-content-between">
              <h5 class="mb-1"><?php echo htmlspecialchars($notification['Message']); ?></h5>
              <small><?php echo $notification['Timestamp']; ?></small>
            </div>
            <?php if (!$notification['IsRead']): ?>
              <small class="text-danger">Unread</small>
            <?php else: ?>
              <small class="text-success">Read</small>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</body>

</html>