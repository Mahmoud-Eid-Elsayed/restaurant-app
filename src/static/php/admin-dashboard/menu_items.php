<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Fetch all menu items with their category names and order counts
    $stmt = $conn->query("
        SELECT 
            mi.*,
            mc.CategoryName,
            COUNT(oi.OrderItemID) as order_count
        FROM MenuItem mi
        INNER JOIN MenuCategory mc ON mi.CategoryID = mc.CategoryID
        LEFT JOIN OrderItem oi ON mi.ItemID = oi.ItemID
        GROUP BY mi.ItemID, mc.CategoryName
        ORDER BY mc.CategoryName, mi.ItemName
    ");
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Menu Items - ELCHEF</title>
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
                <li>
                    <a href="menu_categories.php">
                        <i class="fas fa-list"></i>
                        <span>Categories</span>
                    </a>
                </li>
                <li class="active">
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
            <button type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="main-content">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

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
                    <h2><i class="fas fa-utensils me-3"></i>Menu Items</h2>
                    <a href="add_menu_item.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add New Item</span>
                    </a>
                </div>

                <?php if (!empty($menuItems)): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Item Pic</th>
                                    <th>Availability</th>
                                    <th>Orders</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menuItems as $item): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($item['ItemID']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($item['ItemName']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?php echo htmlspecialchars($item['CategoryName']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">
                                                $<?php echo number_format($item['Price'], 2); ?>
                                            </span>
                                        </td>
                                        <td class="text-muted">
                                            <?php echo htmlspecialchars($item['Description'] ?? 'No description'); ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge <?php echo $item['Availability'] ? 'bg-success' : 'bg-danger'; ?>">
                                                <i
                                                    class="fas <?php echo $item['Availability'] ? 'fa-check-circle' : 'fa-times-circle'; ?> me-1"></i>
                                                <?php if (!empty($item['ImageURL'])): ?>
                                                    <img src="<?php echo htmlspecialchars($item['ImageURL']); ?>" width="50"
                                                        height="50" alt="Item">
                                                <?php else: ?>
                                                    <span>No Image</span>
                                                <?php endif; ?>
                                        </td>
                                        <td>
                                            <span
                                                class="badge <?php echo $item['Availability'] ? 'bg-success' : 'bg-danger'; ?>">

                                                <?php echo $item['Availability'] ? 'Available' : 'Not Available'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-shopping-bag me-1"></i>
                                                <?php echo (int) $item['order_count']; ?> orders
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="edit_menu_item.php?id=<?php echo $item['ItemID']; ?>"
                                                    class="btn btn-warning btn-sm" title="Edit Item">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($item['order_count'] > 0): ?>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        title="Cannot delete: Item has orders" disabled>
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button"
                                                        onclick="showDeleteModal(<?php echo $item['ItemID']; ?>, '<?php echo htmlspecialchars(addslashes($item['ItemName'])); ?>')"
                                                        class="btn btn-danger btn-sm" title="Delete Item">
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
                        No menu items found. Click the "Add New Item" button to create one.
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
                    <p>Are you sure you want to delete the menu item "<span id="itemName"></span>"?</p>
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
                        <i class="fas fa-trash me-2"></i>Delete Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let deleteModal;
        let itemToDelete = null;

        document.addEventListener('DOMContentLoaded', function () {
            deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

            // Set up delete confirmation button
            document.getElementById('confirmDelete').addEventListener('click', function () {
                if (itemToDelete) {
                    window.location.href = `delete_menu_item.php?id=${itemToDelete.id}&token=${Date.now()}`;
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

        function showDeleteModal(itemId, itemName) {
            itemToDelete = {
                id: itemId,
                name: itemName
            };
            document.getElementById('itemName').textContent = itemName;
            deleteModal.show();
        }
    </script>
</body>

</html>