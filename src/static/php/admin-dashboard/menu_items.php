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
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a></li>
                <li class="active"><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
                <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li><a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a></li>
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
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

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
                    <h2>Manage Menu Items</h2>
                    <a href="add_menu_item.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Item
                    </a>
                </div>

                <?php if (!empty($menuItems)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Description</th>
                                    <th>Availability</th>
                                    <th>Orders</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menuItems as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['ItemID']); ?></td>
                                        <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                                        <td><?php echo htmlspecialchars($item['CategoryName']); ?></td>
                                        <td>$<?php echo number_format($item['Price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($item['Description'] ?? 'No description'); ?></td>
                                        <td>
                                            <span
                                                class="badge <?php echo $item['Availability'] ? 'bg-success' : 'bg-danger'; ?>">
                                                <?php echo $item['Availability'] ? 'Available' : 'Not Available'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
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
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the menu item "<span id="itemName"></span>"?
                    <p class="text-danger mt-2 mb-0">
                        <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">
                        <i class="fas fa-trash"></i> Delete Item
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
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