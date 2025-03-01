<?php
session_start();

// Include the database connection
require_once __DIR__ . '/../../connection/db.php';

try {
  // Fetch all admin notifications (where UserID is NULL)
  $query = "SELECT * FROM Notification WHERE UserID IS NULL ORDER BY Timestamp DESC";
  $stmt = $conn->query($query);
  $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Count unread notifications
  $unread_count = 0;
  foreach ($notifications as $notification) {
    if (!$notification['IsRead']) {
      $unread_count++;
    }
  }
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
      <div class="sidebar-header">
        <h3>ELCHEF Admin</h3>
      </div>
      <ul class="list-unstyled components">
        <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a></li>
        <li><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
        <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="special_offers.php"><i class="fas fa-tags"></i> Special Offers</a></li>
        <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
        <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fa-solid fa-truck"></i> Suppliers</a></li>
        <li class="active"><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a></li>
      </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
      <button type="button" id="sidebarToggle" class="btn btn-info">
        <i class="fas fa-bars"></i>
      </button>

      <div class="container mt-5">
        <h2>Admin Notifications 🔔 <span id="notification-count"
            class="badge bg-danger"><?php echo $unread_count; ?></span></h2>
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
    </div>
  </div>

  <!-- Bootstrap and jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>

  <!-- JavaScript to handle notification toggling and count updates -->
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
            // Reload the page to reflect changes
            location.reload();
          } else {
            alert('Error updating notification');
          }
        })
        .catch(error => console.error('Error:', error));
    }

    // Function to fetch and update the unread notification count
    function fetchUnreadNotificationCount() {
      fetch('fetch_unread_count.php?admin=true')
        .then(response => response.json())
        .then(data => {
          const notificationCountElement = document.getElementById('notification-count');
          if (data.unread_count > 0) {
            notificationCountElement.textContent = data.unread_count;
            notificationCountElement.style.display = 'inline-block';
          } else {
            notificationCountElement.textContent = '';
            notificationCountElement.style.display = 'none';
          }
        })
        .catch(error => console.error('Error fetching unread count:', error));
    }

    // Fetch the unread count every 5 seconds
    setInterval(fetchUnreadNotificationCount, 5000);

    // Initial fetch
    fetchUnreadNotificationCount();
  </script>
</body>

</html>