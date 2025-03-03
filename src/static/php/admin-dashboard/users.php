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
  <style>
    .page-header {
      background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
      color: white;
      padding: 2rem;
      border-radius: 1rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .page-header h2 {
      margin: 0;
      color: white;
    }

    .page-header .btn-primary {
      background: rgba(255, 255, 255, 0.2);
      border: none;
      backdrop-filter: blur(10px);
      padding: 0.8rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .page-header .btn-primary:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: translateY(-2px);
    }

    .table-container {
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      padding: 1rem;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table {
      margin-bottom: 0;
      width: 100%;
    }

    .table th {
      background: #f8f9fa;
      white-space: nowrap;
      padding: 1rem;
    }

    .table td {
      vertical-align: middle;
      padding: 0.75rem;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #e9ecef;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.2rem;
      color: #6c757d;
      margin-right: 0.5rem;
    }

    .user-info {
      display: flex;
      align-items: center;
    }

    .user-details {
      display: flex;
      flex-direction: column;
    }

    .user-name {
      font-weight: 500;
      margin-bottom: 0.25rem;
    }

    .user-email {
      font-size: 0.875rem;
      color: #6c757d;
    }

    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 2rem;
      font-weight: 500;
      white-space: nowrap;
    }

    .btn-group .btn {
      padding: 0.375rem 0.75rem;
    }

    .search-box {
      max-width: 300px;
      margin-bottom: 1rem;
    }

    @media (max-width: 1200px) {
      .table-container {
        padding: 0.5rem;
      }

      .table th,
      .table td {
        padding: 0.5rem;
      }

      .btn-group .btn {
        padding: 0.25rem 0.5rem;
      }
    }

    @media (max-width: 991px) {
      .page-header {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .user-avatar {
        width: 32px;
        height: 32px;
        font-size: 1rem;
      }
    }

    @media (max-width: 768px) {
      .page-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
        padding: 1rem;
      }

      .table-responsive {
        margin: 0;
        border: none;
      }

      .table {
        min-width: 450px;
      }

      .user-email {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      .btn-group .btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .btn-group .btn i {
        margin: 0;
        font-size: 0.875rem;
      }

      .search-box {
        max-width: 100%;
      }
    }

    @media (max-width: 576px) {
      .page-header {
        padding: 0.75rem;
        margin-bottom: 1rem;
      }

      .page-header h2 {
        font-size: 1.25rem;
      }

      .page-header .btn-primary {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
      }

      .table {
        min-width: 400px;
      }

      .table th {
        padding: 0.5rem;
        font-size: 0.75rem;
      }

      .table td {
        padding: 0.5rem;
        font-size: 0.875rem;
      }

      .user-avatar {
        width: 24px;
        height: 24px;
        font-size: 0.75rem;
      }

      .user-name {
        font-size: 0.875rem;
      }

      .user-email {
        font-size: 0.75rem;
        max-width: 100px;
      }

      .status-badge {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
      }

      .btn-group .btn {
        width: 28px;
        height: 28px;
      }
    }

    @media (max-width: 480px) {
      .page-header {
        padding: 0.5rem;
      }

      .page-header h2 {
        font-size: 1.125rem;
      }

      .page-header .btn-primary {
        padding: 0.375rem 0.75rem;
        font-size: 0.8125rem;
      }

      .table {
        min-width: 350px;
      }

      .table th {
        padding: 0.375rem;
        font-size: 0.75rem;
      }

      .table td {
        padding: 0.375rem;
        font-size: 0.8125rem;
      }

      .user-avatar {
        width: 20px;
        height: 20px;
        font-size: 0.625rem;
        margin-right: 0.25rem;
      }

      .user-name {
        font-size: 0.8125rem;
      }

      .user-email {
        font-size: 0.6875rem;
        max-width: 80px;
      }

      .status-badge {
        padding: 0.125rem 0.375rem;
        font-size: 0.6875rem;
      }

      .btn-group .btn {
        width: 24px;
        height: 24px;
      }

      .btn-group .btn i {
        font-size: 0.75rem;
      }
    }
  </style>
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
          <a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a>
        </li>
        <li>
          <a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a>
        </li>
        <li>
          <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
        </li>
        <li><a href="special_offers.php"><i class="fas fa-tags"></i>Special Offers</a></li>
        <li>
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
      <!-- Toggle Button -->
      <button type="button" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>

      <div class="main-content">
        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['message'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="page-header d-flex justify-content-between align-items-center">
          <h2><i class="fas fa-users me-2"></i>Manage Users</h2>
          <a href="add_user.php" class="btn btn-primary">
            <i class="fas fa-user-plus me-2"></i>Add New User
          </a>
        </div>

        <?php if (!empty($users)): ?>
          <div class="table-container">
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>User Information</th>
                    <th>Role</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($users as $user): ?>
                    <tr class="<?php echo $user['Status'] === 'inactive' ? 'table-secondary' : ''; ?>">
                      <td><?php echo htmlspecialchars($user['UserID']); ?></td>
                      <td>
                        <div class="user-info">
                          <div class="user-avatar">
                            <i class="fas fa-user"></i>
                          </div>
                          <div class="user-details">
                            <span class="user-name"><?php echo htmlspecialchars($user['Username']); ?></span>
                            <span class="user-email">
                              <?php
                              $fullName = trim($user['FirstName'] . ' ' . $user['LastName']);
                              echo htmlspecialchars($fullName ?: 'No name provided');
                              ?>
                            </span>
                          </div>
                        </div>
                      </td>
                      <td>
                        <span class="badge <?php
                        echo $user['Role'] === 'admin' ? 'bg-danger' :
                          ($user['Role'] === 'staff' ? 'bg-warning' : 'bg-info');
                        ?>">
                          <?php echo ucfirst(htmlspecialchars($user['Role'])); ?>
                        </span>
                      </td>
                      <td>
                        <div class="user-details">
                          <?php if ($user['Email']): ?>
                            <span class="user-email">
                              <i class="fas fa-envelope me-1"></i>
                              <?php echo htmlspecialchars($user['Email']); ?>
                            </span>
                          <?php endif; ?>
                          <?php if ($user['PhoneNumber']): ?>
                            <span class="user-email">
                              <i class="fas fa-phone me-1"></i>
                              <?php echo htmlspecialchars($user['PhoneNumber']); ?>
                            </span>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td>
                        <span class="badge <?php echo $user['Status'] === 'active' ? 'bg-success' : 'bg-secondary'; ?>">
                          <?php echo ucfirst(htmlspecialchars($user['Status'])); ?>
                        </span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="edit_user.php?id=<?php echo $user['UserID']; ?>" class="btn btn-warning" title="Edit">
                            <i class="fas fa-edit"></i>
                          </a>
                          <?php if ($user['Status'] === 'active'): ?>
                            <button
                              onclick="confirmDelete(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars(addslashes($user['Username'])); ?>')"
                              class="btn btn-danger" title="Deactivate">
                              <i class="fas fa-user-slash"></i>
                            </button>
                          <?php else: ?>
                            <button
                              onclick="confirmReactivate(<?php echo $user['UserID']; ?>, '<?php echo htmlspecialchars(addslashes($user['Username'])); ?>')"
                              class="btn btn-success" title="Reactivate">
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
          </div>
        <?php else: ?>
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No users found in the system.
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
    document.addEventListener('DOMContentLoaded', function () {
      setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alert) {
          if (alert && typeof bootstrap !== 'undefined') {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
          }
        });
      }, 5000);
    });

    // Add scroll indicator for table
    document.addEventListener('DOMContentLoaded', function () {
      const tableContainer = document.querySelector('.table-container');
      const tableResponsive = document.querySelector('.table-responsive');

      if (tableResponsive && tableContainer) {
        function checkScroll() {
          if (tableResponsive.scrollWidth > tableResponsive.clientWidth) {
            tableContainer.classList.add('has-scroll');
          } else {
            tableContainer.classList.remove('has-scroll');
          }
        }

        // Check on load and resize
        checkScroll();
        window.addEventListener('resize', checkScroll);

        // Check on scroll
        tableResponsive.addEventListener('scroll', function () {
          if (tableResponsive.scrollLeft + tableResponsive.clientWidth >= tableResponsive.scrollWidth - 30) {
            tableContainer.classList.remove('has-scroll');
          } else {
            tableContainer.classList.add('has-scroll');
          }
        });
      }

      // ... existing DOMContentLoaded code ...
    });
  </script>
</body>

</html>