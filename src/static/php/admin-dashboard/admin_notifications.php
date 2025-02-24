<?php
session_start();

// Include the database connection
require_once __DIR__ . '/../../connection/db.php';

try {
  // Mark all admin notifications as read (where UserID is NULL)
  $query = "UPDATE Notification SET IsRead = TRUE WHERE UserID IS NULL";
  $stmt = $conn->prepare($query);
  $stmt->execute();

  // Fetch notifications for the admin
  $query = "SELECT * FROM Notification WHERE UserID IS NULL ORDER BY Timestamp DESC";
  $stmt = $conn->query($query);
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
  <title>Admin Notifications</title>
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
      <div class="sidebar-header">
        <h3>ELCHEF Admin</h3>
      </div>
      <ul class="list-unstyled components">
        <li>
          <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        </li>
        <li>
          <a href="users.php"><i class="fas fa-users"></i> Users</a>
        </li>
        <li>
          <a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a>
        </li>
        <li>
          <a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a>
        </li>
        <li>
          <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        </li>
        <li class="active">
          <a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a>
        </li>
        <li>
          <a href="inventory.php"><i class="fas fa-box"></i> Inventory</a>
        </li>
        <li>
          <a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a>
        </li>
        <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a>

      </ul>

    </nav>

    <!-- Page Content -->
    <div id="content">
      <button type="button" id="sidebarToggle" class="btn btn-info">
        <i class="fas fa-bars"></i>
      </button>

      <div class="container mt-5">
        <h2>Admin Notifications 🔔</h2>
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
                <button class="btn btn-sm <?php echo $notification['IsRead'] ? 'btn-success' : 'btn-warning'; ?>" onclick="toggleReadStatus(<?php echo $notification['NotificationID']; ?>,
                  <?php echo $notification['IsRead']; ?>)">
                  <?php echo $notification['IsRead'] ? 'Read ✅' : 'Mark as Read 🔴'; ?>
                </button>
              </div>
            <?php endforeach; ?>

          <?php endif; ?>
        </div>
      </div>
    </div>
    <script src="../../../../src/assets/libraries/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>

    <script>
      function toggleReadStatus(notificationId, isRead) {
        fetch('toggle_notification.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            notification_id: notificationId,
            is_read: isRead
          })
        })
          .then(response => {
            if (!response.ok) {
              throw new Error('Network response was not ok');
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              // Reload the page or update the UI accordingly
              location.reload();
            } else {
              alert('Failed to update notification status: ' + data.error);
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
      }
    </script>
</body>

</html>