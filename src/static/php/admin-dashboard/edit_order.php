<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orders.php?error=' . urlencode('Invalid order ID'));
    exit;
}

$orderId = (int)$_GET['id'];
$error = null;
$success = null;

try {
    // Fetch order details with customer information
    $stmt = $conn->prepare("
        SELECT o.*, u.Username, u.FirstName, u.LastName, u.Email, u.PhoneNumber
        FROM `Order` o
        INNER JOIN User u ON o.CustomerID = u.UserID
        WHERE o.OrderID = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header('Location: orders.php?error=' . urlencode('Order not found'));
        exit;
    }

    // Check if order can be edited based on its status
    $canEdit = !in_array($order['OrderStatus'], ['Delivered', 'Cancelled']);

    // Fetch order items
    $stmt = $conn->prepare("
        SELECT oi.*, mi.ItemName, mi.Price as CurrentPrice
        FROM OrderItem oi
        INNER JOIN MenuItem mi ON oi.ItemID = mi.ItemID
        WHERE oi.OrderID = ?
    ");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all available menu items grouped by category
    $stmt = $conn->prepare("
        SELECT mi.*, mc.CategoryName
        FROM MenuItem mi
        INNER JOIN MenuCategory mc ON mi.CategoryID = mc.CategoryID
        WHERE mi.Availability = 1
        ORDER BY mc.CategoryName, mi.ItemName
    ");
    $stmt->execute();
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $menuItemsByCategory = [];
    foreach ($menuItems as $item) {
        $menuItemsByCategory[$item['CategoryName']][] = $item;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate status change
        $newStatus = $_POST['status'] ?? '';
        $validStatuses = ['Pending', 'Preparing', 'Ready', 'Delivered', 'Cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception('Invalid order status');
        }

        // Start transaction
        $conn->beginTransaction();

        // Update order status
        if ($newStatus !== $order['OrderStatus']) {
            // Log status change in OrderStatusHistory
            $stmt = $conn->prepare("
                INSERT INTO OrderStatusHistory (OrderID, StatusFrom, StatusTo, ChangedAt, Notes)
                VALUES (?, ?, ?, NOW(), ?)
            ");
            $stmt->execute([
                $orderId,
                $order['OrderStatus'],
                $newStatus,
                'Status updated via admin dashboard'
            ]);

            // Update order status
            $stmt = $conn->prepare("UPDATE `Order` SET OrderStatus = ? WHERE OrderID = ?");
            $stmt->execute([$newStatus, $orderId]);
        }

        // Update order items if order is not delivered/cancelled
        if ($canEdit) {
            // Validate that at least one item exists
            if (empty($_POST['items'])) {
                throw new Exception('Order must contain at least one item');
            }

            // Delete existing order items
            $stmt = $conn->prepare("DELETE FROM OrderItem WHERE OrderID = ?");
            $stmt->execute([$orderId]);

            // Insert updated order items
            $totalAmount = 0;
            $stmt = $conn->prepare("
                INSERT INTO OrderItem (OrderID, ItemID, Quantity, PriceAtTimeOfOrder)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($_POST['items'] as $item) {
                $itemId = (int)$item['id'];
                $quantity = (int)$item['quantity'];
                $price = (float)$item['price'];

                if ($quantity <= 0) continue;

                $stmt->execute([$orderId, $itemId, $quantity, $price]);
                $totalAmount += $quantity * $price;
            }

            // Update order total
            $stmt = $conn->prepare("UPDATE `Order` SET TotalAmount = ? WHERE OrderID = ?");
            $stmt->execute([$totalAmount, $orderId]);
        }

        // Commit transaction
        $conn->commit();
        $success = 'Order updated successfully';

        // Refresh order data
        header("Location: edit_order.php?id=$orderId&message=" . urlencode($success));
        exit;
    }
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order - ELCHEF</title>
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
                <li><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li class="active"><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
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

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Edit Order #<?php echo $orderId; ?></h2>
                    <a href="orders.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Customer Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars(trim($order['FirstName'] . ' ' . $order['LastName'])); ?></p>
                                <p><strong>Username:</strong> <?php echo htmlspecialchars($order['Username']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['Email'] ?? 'N/A'); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['PhoneNumber'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <form method="POST" id="orderForm" class="needs-validation" novalidate>
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Order Details</h5>
                                    <?php if ($canEdit): ?>
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Order Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="Pending" <?php echo $order['OrderStatus'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Preparing" <?php echo $order['OrderStatus'] === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                            <option value="Ready" <?php echo $order['OrderStatus'] === 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                            <option value="Delivered" <?php echo $order['OrderStatus'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="Cancelled" <?php echo $order['OrderStatus'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="orderItemsTable">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Price</th>
                                                    <th>Quantity</th>
                                                    <th>Total</th>
                                                    <?php if ($canEdit): ?>
                                                        <th>Actions</th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($orderItems as $item): ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($item['ItemName']); ?></td>
                                                        <td>$<?php echo number_format($item['PriceAtTimeOfOrder'], 2); ?></td>
                                                        <td>
                                                            <?php if ($canEdit): ?>
                                                                <input type="number" class="form-control quantity-input" 
                                                                       name="items[<?php echo $item['ItemID']; ?>][quantity]"
                                                                       value="<?php echo $item['Quantity']; ?>" 
                                                                       min="1" required>
                                                                <input type="hidden" name="items[<?php echo $item['ItemID']; ?>][id]" 
                                                                       value="<?php echo $item['ItemID']; ?>">
                                                                <input type="hidden" name="items[<?php echo $item['ItemID']; ?>][price]" 
                                                                       value="<?php echo $item['PriceAtTimeOfOrder']; ?>">
                                                            <?php else: ?>
                                                                <?php echo $item['Quantity']; ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="item-total">$<?php echo number_format($item['Quantity'] * $item['PriceAtTimeOfOrder'], 2); ?></td>
                                                        <?php if ($canEdit): ?>
                                                            <td>
                                                                <button type="button" class="btn btn-danger btn-sm remove-item">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </td>
                                                        <?php endif; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="<?php echo $canEdit ? '3' : '2'; ?>" class="text-end"><strong>Total:</strong></td>
                                                    <td colspan="<?php echo $canEdit ? '2' : '1'; ?>" class="order-total">
                                                        $<?php echo number_format($order['TotalAmount'], 2); ?>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-end">
                                        <a href="orders.php" class="btn btn-secondary me-2">Cancel</a>
                                        <?php if ($canEdit): ?>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> Save Changes
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Add Item to Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="itemSearch" placeholder="Search items...">
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="list-group" id="categoryList">
                                <a href="#" class="list-group-item list-group-item-action active" data-category="all">
                                    All Categories
                                </a>
                                <?php foreach (array_keys($menuItemsByCategory) as $category): ?>
                                    <a href="#" class="list-group-item list-group-item-action" data-category="<?php echo htmlspecialchars($category); ?>">
                                        <?php echo htmlspecialchars($category); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="list-group" id="itemList">
                                <?php foreach ($menuItems as $item): ?>
                                    <a href="#" class="list-group-item list-group-item-action item-entry" 
                                       data-id="<?php echo $item['ItemID']; ?>"
                                       data-name="<?php echo htmlspecialchars($item['ItemName']); ?>"
                                       data-price="<?php echo $item['Price']; ?>"
                                       data-category="<?php echo htmlspecialchars($item['CategoryName']); ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($item['ItemName']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($item['CategoryName']); ?></small>
                                            </div>
                                            <span class="badge bg-primary">$<?php echo number_format($item['Price'], 2); ?></span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const orderForm = document.getElementById('orderForm');
        const itemSearch = document.getElementById('itemSearch');
        const categoryList = document.getElementById('categoryList');
        const itemList = document.getElementById('itemList');
        const orderItemsTable = document.getElementById('orderItemsTable');
        const addItemModal = new bootstrap.Modal(document.getElementById('addItemModal'));
        
        // Function to update order total
        function updateOrderTotal() {
            let total = 0;
            document.querySelectorAll('.item-total').forEach(cell => {
                total += parseFloat(cell.textContent.replace('$', ''));
            });
            document.querySelector('.order-total').textContent = '$' + total.toFixed(2);
        }

        // Function to update item total
        function updateItemTotal(row) {
            const quantity = parseInt(row.querySelector('.quantity-input').value);
            const price = parseFloat(row.querySelector('input[name$="[price]"]').value);
            const total = quantity * price;
            row.querySelector('.item-total').textContent = '$' + total.toFixed(2);
            updateOrderTotal();
        }

        // Handle quantity changes
        orderItemsTable.addEventListener('change', function(e) {
            if (e.target.classList.contains('quantity-input')) {
                updateItemTotal(e.target.closest('tr'));
            }
        });

        // Handle item removal
        orderItemsTable.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                e.target.closest('tr').remove();
                updateOrderTotal();
            }
        });

        // Handle category selection
        categoryList.addEventListener('click', function(e) {
            if (e.target.classList.contains('list-group-item')) {
                e.preventDefault();
                document.querySelectorAll('#categoryList .list-group-item').forEach(item => {
                    item.classList.remove('active');
                });
                e.target.classList.add('active');
                
                const selectedCategory = e.target.dataset.category;
                document.querySelectorAll('#itemList .item-entry').forEach(item => {
                    if (selectedCategory === 'all' || item.dataset.category === selectedCategory) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
        });

        // Handle item search
        itemSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('#itemList .item-entry').forEach(item => {
                const itemName = item.dataset.name.toLowerCase();
                const itemCategory = item.dataset.category.toLowerCase();
                if (itemName.includes(searchTerm) || itemCategory.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Handle item selection
        itemList.addEventListener('click', function(e) {
            e.preventDefault();
            const item = e.target.closest('.item-entry');
            if (!item) return;

            // Check if item already exists in order
            const existingItem = document.querySelector(`input[name="items[${item.dataset.id}][id]"]`);
            if (existingItem) {
                const row = existingItem.closest('tr');
                const quantityInput = row.querySelector('.quantity-input');
                quantityInput.value = parseInt(quantityInput.value) + 1;
                updateItemTotal(row);
                addItemModal.hide();
                return;
            }

            // Add new item to order
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${item.dataset.name}</td>
                <td>$${parseFloat(item.dataset.price).toFixed(2)}</td>
                <td>
                    <input type="number" class="form-control quantity-input" 
                           name="items[${item.dataset.id}][quantity]"
                           value="1" min="1" required>
                    <input type="hidden" name="items[${item.dataset.id}][id]" 
                           value="${item.dataset.id}">
                    <input type="hidden" name="items[${item.dataset.id}][price]" 
                           value="${item.dataset.price}">
                </td>
                <td class="item-total">$${parseFloat(item.dataset.price).toFixed(2)}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            
            orderItemsTable.querySelector('tbody').appendChild(newRow);
            updateOrderTotal();
            addItemModal.hide();
        });

        // Form validation
        orderForm.addEventListener('submit', function(e) {
            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    });
    </script>
</body>
</html>