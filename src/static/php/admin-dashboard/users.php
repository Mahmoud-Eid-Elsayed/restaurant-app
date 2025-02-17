<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = null;
$users = [];

try {
    // First check if the Status column exists
    $checkColumn = $conn->query("SHOW COLUMNS FROM User LIKE 'Status'");
    if ($checkColumn->rowCount() === 0) {
        // Begin transaction for schema update
        $conn->beginTransaction();
        try {
            // Add Status column
            $conn->exec("ALTER TABLE User ADD COLUMN Status ENUM('active', 'inactive') DEFAULT 'active'");
            // Update existing records
            $conn->exec("UPDATE User SET Status = 'active' WHERE Status IS NULL");
            $conn->commit();
        } catch (PDOException $e) {
            $conn->rollBack();
            throw new PDOException("Failed to update schema: " . $e->getMessage());
        }
    }

    // Prepare the query with proper role validation
    $query = "SELECT 
                UserID,
                Username,
                CASE 
                    WHEN Role IN ('admin', 'staff', 'customer') THEN Role 
                    ELSE 'customer' 
                END as Role,
                Email,
                COALESCE(Status, 'active') as Status,
                FirstName,
                LastName,
                PhoneNumber
              FROM User 
              ORDER BY 
                FIELD(Role, 'admin', 'staff', 'customer'),
                FIELD(Status, 'active', 'inactive'),
                Username ASC";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - ELCHEF</title>
  <!-- Include Bootstrap CSS and Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
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
        <li class="active">
          <a href="users.php"><i class="fas fa-users"></i> Users</a>
        </li>
        <li>
          <a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a>
        </li>
        <li>
          <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        </li>
        <li>
          <a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a>
        </li>
      </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
      <!-- Toggle Button -->
      <button type="button" id="sidebarToggle" class="btn btn-info">
        <i class="fas fa-bars"></i>
      </button>

      <div class="main-content">
        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['message'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2>Manage Users</h2>
          <a href="add_user.php" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add New User
          </a>
        </div>

        <?php if (!empty($users)): ?>
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Name</th>
                  <th>Role</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                  <tr class="<?php echo $user['Status'] === 'inactive' ? 'table-secondary' : ''; ?>">
                    <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                    <td><?php echo htmlspecialchars($user['Username']); ?></td>
                    <td>
                      <?php 
                      $fullName = trim($user['FirstName'] . ' ' . $user['LastName']);
                      echo htmlspecialchars($fullName ?: 'N/A');
                      ?>
                    </td>
                    <td>
                      <span class="badge <?php 
                        echo $user['Role'] === 'admin' ? 'bg-danger' : 
                          ($user['Role'] === 'staff' ? 'bg-warning' : 'bg-info'); 
                      ?>">
                        <?php echo ucfirst(htmlspecialchars($user['Role'])); ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars($user['Email'] ?: 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($user['PhoneNumber'] ?: 'N/A'); ?></td>
                    <td>
                      <span class="badge <?php echo $user['Status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                        <?php echo ucfirst(htmlspecialchars($user['Status'])); ?>
                      </span>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <a href="edit_user.php?id=<?php echo $user['UserID']; ?>" 
                           class="btn btn-warning btn-sm" 
                           title="Edit">
                          <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($user['Status'] === 'active'): ?>
                          <button onclick="confirmDelete(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars(addslashes($user['Username'])); ?>')" 
                                  class="btn btn-danger btn-sm"
                                  title="Deactivate">
                            <i class="fas fa-user-slash"></i>
                          </button>
                        <?php else: ?>
                          <button onclick="confirmReactivate(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars(addslashes($user['Username'])); ?>')" 
                                  class="btn btn-success btn-sm"
                                  title="Reactivate">
                            <i class="fas fa-user-check"></i>
                          </button>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info">
            No users found in the system.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
  <script>
    function confirmDelete(userId, username) {
      if (confirm(`Are you sure you want to deactivate user "${username}"? Their orders and data will be preserved.`)) {
        window.location.href = `delete_user.php?id=${userId}&action=deactivate`;
      }
    }

    function confirmReactivate(userId, username) {
      if (confirm(`Are you sure you want to reactivate user "${username}"?`)) {
        window.location.href = `delete_user.php?id=${userId}&action=reactivate`;
      }
    }

    // Auto-close alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
          if (alert && typeof bootstrap !== 'undefined') {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
          }
        });
      }, 5000);
    });
  </script>
</body>
</html>