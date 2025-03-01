<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
  // Fetch all menu categories with their item counts
  $stmt = $conn->query("
        SELECT 
            mc.*,
            COUNT(mi.ItemID) as item_count
        FROM MenuCategory mc
        LEFT JOIN MenuItem mi ON mc.CategoryID = mi.CategoryID
        GROUP BY mc.CategoryID
        ORDER BY mc.CategoryName ASC
    ");
  $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Menu Categories - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
          <a href="index.php">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li>
          <a href="users.php">
            <i class="fas fa-users"></i>
            <span>Users</span>
          </a>
        </li>
        <li class="active">
          <a href="menu_categories.php">
            <i class="fas fa-list"></i>
            <span>Categories</span>
          </a>
        </li>
        <li>
          <a href="menu_items.php">
            <i class="fas fa-utensils"></i>
            <span>Menu Items</span>
          </a>
        </li>
        <li>
          <a href="orders.php">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders</span>
          </a>
        </li>
        <li><a href="special_offers.php"><i class="fas fa-tags"></i> Special Offers</a></li>

        <li>
          <a href="reservations.php">
            <i class="fas fa-calendar-alt"></i>
            <span>Reservations</span>
          </a>
        </li>
        <li>
          <a href="inventory.php">
            <i class="fas fa-box"></i>
            <span>Inventory</span>
          </a>
        </li>
        <li>
          <a href="suppliers.php">
            <i class="fas fa-truck"></i>
            <span>Suppliers</span>
          </a>
        </li>
        <li>
          <a href="admin_notifications.php">
            <i class="fas fa-bell"></i>
            <span>Notifications</span>
          </a>
        </li>
      </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
      <!-- Toggle Button -->
      <button type="button" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>

      <div class="main-content">
        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['message'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2><i class="fas fa-list me-3"></i>Menu Categories</h2>
          <a href="add_menu_category.php" class="btn btn-primary">
            <i class="fas fa-plus"></i>
            <span>Add New Category</span>
          </a>
        </div>

        <?php if (!empty($categories)): ?>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Category Name</th>
                  <th>Description</th>
                  <th>Items Count</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($categories as $category): ?>
                  <tr>
                    <td>#<?php echo htmlspecialchars($category['CategoryID']); ?></td>
                    <td>
                      <strong><?php echo htmlspecialchars($category['CategoryName']); ?></strong>
                    </td>
                    <td><?php echo htmlspecialchars($category['Description'] ?? 'No description'); ?></td>
                    <td>
                      <span class="badge bg-info">
                        <?php echo (int) $category['item_count']; ?> items
                      </span>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <a href="edit_menu_category.php?id=<?php echo $category['CategoryID']; ?>"
                          class="btn btn-warning btn-sm" title="Edit Category">
                          <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($category['item_count'] > 0): ?>
                          <button type="button" class="btn btn-danger btn-sm" title="Cannot delete: Category has items"
                            disabled>
                            <i class="fas fa-trash"></i>
                          </button>
                        <?php else: ?>
                          <button type="button"
                            onclick="showDeleteModal(<?php echo $category['CategoryID']; ?>, '<?php echo htmlspecialchars(addslashes($category['CategoryName'])); ?>')"
                            class="btn btn-danger btn-sm" title="Delete Category">
                            <i class="fas fa-trash"></i>
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
            <i class="fas fa-info-circle me-2"></i>
            No menu categories found. Click the "Add New Category" button to create one.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">
            <i class="fas fa-trash-alt me-2"></i>Confirm Deletion
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete the category "<span id="categoryName"></span>"?</p>
          <p class="text-danger mt-2 mb-0">
            <i class="fas fa-exclamation-triangle me-2"></i>
            This action cannot be undone.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Cancel
          </button>
          <button type="button" class="btn btn-danger" id="confirmDelete">
            <i class="fas fa-trash me-2"></i>Delete Category
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let deleteModal;
    let categoryToDelete = null;

    document.addEventListener('DOMContentLoaded', function () {
      deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

      // Set up delete confirmation button
      document.getElementById('confirmDelete').addEventListener('click', function () {
        if (categoryToDelete) {
          window.location.href = `delete_menu_category.php?id=${categoryToDelete.id}&token=${Date.now()}`;
        }
        deleteModal.hide();
      });

      // Auto-close alerts after 5 seconds
      setTimeout(function () {
        document.querySelectorAll('.alert').forEach(function (alert) {
          if (alert && typeof bootstrap !== 'undefined') {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
          }
        });
      }, 5000);

      // Toggle sidebar
      document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('active');
      });
    });

    function showDeleteModal(categoryId, categoryName) {
      categoryToDelete = {
        id: categoryId,
        name: categoryName
      };
      document.getElementById('categoryName').textContent = categoryName;
      deleteModal.show();
    }
  </script>
</body>

</html>