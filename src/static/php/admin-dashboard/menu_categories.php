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
        <li>
          <a href="users.php"><i class="fas fa-users"></i> Users</a>
        </li>
        <li class="active">
          <a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a>
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
        <li>
          <a href="inventory.php"><i class="fas fa-box"></i> Inventory</a>
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
            <?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['message'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Manage Menu Categories</h2>
          <a href="add_menu_category.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Category
          </a>
        </div>

        <?php if (!empty($categories)): ?>
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead class="table-light">
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
                    <td><?php echo htmlspecialchars($category['CategoryID']); ?></td>
                    <td><?php echo htmlspecialchars($category['CategoryName']); ?></td>
                    <td><?php echo htmlspecialchars($category['Description'] ?? 'No description'); ?></td>
                    <td>
                      <span class="badge bg-info">
                        <?php echo (int)$category['item_count']; ?> items
                      </span>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <a href="edit_menu_category.php?id=<?php echo $category['CategoryID']; ?>" 
                           class="btn btn-warning btn-sm" 
                           title="Edit Category">
                          <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($category['item_count'] > 0): ?>
                          <button type="button" 
                                  class="btn btn-danger btn-sm" 
                                  title="Cannot delete: Category has items"
                                  disabled>
                            <i class="fas fa-trash"></i>
                          </button>
                        <?php else: ?>
                          <button type="button"
                                  onclick="showDeleteModal(<?php echo $category['CategoryID']; ?>, '<?php echo htmlspecialchars(addslashes($category['CategoryName'])); ?>')"
                                  class="btn btn-danger btn-sm"
                                  title="Delete Category">
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
          <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete the category "<span id="categoryName"></span>"?
          <p class="text-danger mt-2 mb-0">
            <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" id="confirmDelete">
            <i class="fas fa-trash"></i> Delete Category
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
  <script>
    let deleteModal;
    let categoryToDelete = null;

    document.addEventListener('DOMContentLoaded', function() {
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        // Set up delete confirmation button
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (categoryToDelete) {
                window.location.href = `delete_menu_category.php?id=${categoryToDelete.id}&token=${Date.now()}`;
            }
            deleteModal.hide();
        });

        // Auto-close alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                if (alert && typeof bootstrap !== 'undefined') {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);
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