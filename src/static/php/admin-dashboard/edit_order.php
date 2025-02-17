<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$error = null;
$order = null;
$orderItems = [];
$menuItems = [];

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orders.php?error=' . urlencode('Invalid order ID'));
    exit;
}

$orderId = (int)$_GET['id'];

try {
    // Start transaction
    $conn->beginTransaction();

    // Fetch order details with customer information
    $stmt = $conn->prepare("
        SELECT 
            o.*,
            u.Username,
            u.FirstName,
            u.LastName,
            u.Email,
            u.PhoneNumber
        FROM `Order` o
        INNER JOIN User u ON o.CustomerID = u.UserID
        WHERE o.OrderID = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Order not found');
    }

    // Check if order can be edited
    if ($order['OrderStatus'] === 'Completed' || $order['OrderStatus'] === 'Cancelled' || $order['OrderStatus'] === 'Refunded') {
        throw new Exception('This order cannot be edited because it is ' . strtolower($order['OrderStatus']));
    }

    // Fetch order items with menu item details
    $stmt = $conn->prepare("
        SELECT 
            oi.*,
            mi.ItemName,
            mi.Price as CurrentPrice,
            mi.Description,
            mc.CategoryName
        FROM OrderItem oi
        INNER JOIN MenuItem mi ON oi.ItemID = mi.ItemID
        LEFT JOIN MenuCategory mc ON mi.CategoryID = mc.CategoryID
        WHERE oi.OrderID = ?
        ORDER BY mc.CategoryName, mi.ItemName
    ");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all available menu items
    $stmt = $conn->prepare("
        SELECT 
            mi.*,
            mc.CategoryName
        FROM MenuItem mi
        INNER JOIN MenuCategory mc ON mi.CategoryID = mc.CategoryID
        WHERE mi.Availability = 1
        ORDER BY mc.CategoryName, mi.ItemName
    ");
    $stmt->execute();
    $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Group menu items by category for easier display
    $menuItemsByCategory = [];
    foreach ($menuItems as $item) {
        $menuItemsByCategory[$item['CategoryName']][] = $item;
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate status change
        $newStatus = $_POST['status'] ?? '';
        $validStatuses = ['Pending', 'Preparing', 'Ready', 'Delivered', 'Cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception('Invalid order status');
        }

        // Get current items for comparison
        $currentItems = [];
        foreach ($orderItems as $item) {
            $currentItems[$item['ItemID']] = $item;
        }

        // Process updated items
        $updatedItems = [];
        $quantities = $_POST['quantity'] ?? [];
        $prices = $_POST['price'] ?? [];
        
        foreach ($quantities as $itemId => $quantity) {
            if ($quantity > 0) {
                $updatedItems[$itemId] = [
                    'quantity' => (int)$quantity,
                    'price' => (float)($prices[$itemId] ?? 0)
                ];
            }
        }

        // Validate that there's at least one item
        if (empty($updatedItems)) {
            throw new Exception('Order must have at least one item');
        }

        // Calculate new total
        $totalAmount = 0;
        foreach ($updatedItems as $itemId => $details) {
            $totalAmount += $details['quantity'] * $details['price'];
        }

        // Update order status and total
        $stmt = $conn->prepare("
            UPDATE `Order` 
            SET OrderStatus = ?,
                TotalAmount = ?,
                LastModified = CURRENT_TIMESTAMP
            WHERE OrderID = ?
        ");
        $stmt->execute([$newStatus, $totalAmount, $orderId]);

        // Log status change if status has changed
        if ($newStatus !== $order['OrderStatus']) {
            $stmt = $conn->prepare("
                INSERT INTO OrderStatusHistory (
                    OrderID,
                    StatusFrom,
                    StatusTo,
                    ChangedAt,
                    Notes
                ) VALUES (?, ?, ?, CURRENT_TIMESTAMP, ?)
            ");
            $stmt->execute([
                $orderId,
                $order['OrderStatus'],
                $newStatus,
                'Status updated during order edit'
            ]);
        }

        // Update order items
        // First, remove items that are no longer in the order
        $stmt = $conn->prepare("DELETE FROM OrderItem WHERE OrderID = ? AND ItemID NOT IN (" . 
            implode(',', array_keys($updatedItems)) . ")");
        $stmt->execute([$orderId]);

        // Then update or insert items
        foreach ($updatedItems as $itemId => $details) {
            if (isset($currentItems[$itemId])) {
                // Update existing item
                $stmt = $conn->prepare("
                    UPDATE OrderItem 
                    SET Quantity = ?,
                        PriceAtTimeOfOrder = ?
                    WHERE OrderID = ? AND ItemID = ?
                ");
                $stmt->execute([
                    $details['quantity'],
                    $details['price'],
                    $orderId,
                    $itemId
                ]);
            } else {
                // Insert new item
                $stmt = $conn->prepare("
                    INSERT INTO OrderItem (
                        OrderID,
                        ItemID,
                        Quantity,
                        PriceAtTimeOfOrder
                    ) VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([
                    $orderId,
                    $itemId,
                    $details['quantity'],
                    $details['price']
                ]);
            }
        }

        // Commit the transaction
        $conn->commit();

        // Redirect back to view order page
        header('Location: view_order.php?id=' . $orderId . '&message=' . urlencode('Order updated successfully'));
        exit;
    }

} catch (Exception $e) {
    // Rollback the transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    $error = $e->getMessage();
    error_log("Error in edit_order.php: " . $e->getMessage());
} catch (PDOException $e) {
    // Rollback the transaction on database error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    $error = "Database error occurred";
    error_log("Database error in edit_order.php: " . $e->getMessage());
}

// Define status colors (matching orders.php)
$statusColors = [
    'Pending' => 'warning',
    'Preparing' => 'info',
    'Ready' => 'success',
    'Delivered' => 'success',
    'Cancelled' => 'danger',
    'Refunded' => 'secondary'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order #<?php echo $orderId; ?> - ELCHEF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
    <style>
        .menu-item-row:hover {
            background-color: #f8f9fa;
        }
        .quantity-input {
            width: 80px;
        }
        .price-input {
            width: 100px;
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
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a></li>
                <li><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li class="active"><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
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
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                        <br>
                        <a href="orders.php" class="btn btn-secondary mt-2">Back to Orders</a>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>
                            Edit Order #<?php echo htmlspecialchars($order['OrderID']); ?>
                            <span class="badge bg-<?php echo $statusColors[$order['OrderStatus']] ?? 'secondary'; ?>">
                                <?php echo htmlspecialchars($order['OrderStatus']); ?>
                            </span>
                        </h2>
                        <div class="btn-group">
                            <a href="view_order.php?id=<?php echo $order['OrderID']; ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel Edit
                            </a>
                            <button type="submit" form="editOrderForm" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <!-- Order Items Form -->
                            <form id="editOrderForm" method="POST" class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-shopping-cart"></i> Order Items
                                    </h5>
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                        <i class="fas fa-plus"></i> Add Item
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="orderItemsTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Category</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($orderItems as $item): ?>
                                                    <tr class="menu-item-row" data-item-id="<?php echo $item['ItemID']; ?>">
                                                        <td>
                                                            <?php echo htmlspecialchars($item['ItemName']); ?>
                                                            <?php if ($item['Description']): ?>
                                                                <br>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars($item['Description']); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($item['CategoryName']); ?></td>
                                                        <td>
                                                            <input type="number" 
                                                                   class="form-control quantity-input" 
                                                                   name="quantity[<?php echo $item['ItemID']; ?>]" 
                                                                   value="<?php echo (int)$item['Quantity']; ?>"
                                                                   min="1"
                                                                   required
                                                                   onchange="updateTotal(this)">
                                                        </td>
                                                        <td>
                                                            <input type="number" 
                                                                   class="form-control price-input" 
                                                                   name="price[<?php echo $item['ItemID']; ?>]" 
                                                                   value="<?php echo number_format($item['PriceAtTimeOfOrder'], 2); ?>"
                                                                   step="0.01"
                                                                   min="0"
                                                                   required
                                                                   onchange="updateTotal(this)">
                                                        </td>
                                                        <td class="item-total">
                                                            $<?php echo number_format($item['Quantity'] * $item['PriceAtTimeOfOrder'], 2); ?>
                                                        </td>
                                                        <td>
                                                            <button type="button" 
                                                                    class="btn btn-danger btn-sm"
                                                                    onclick="removeItem(this)">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <td colspan="4" class="text-end">
                                                        <strong>Total Amount:</strong>
                                                    </td>
                                                    <td colspan="2" id="orderTotal">
                                                        <strong>
                                                            $<?php echo number_format($order['TotalAmount'], 2); ?>
                                                        </strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <div class="mt-3">
                                        <label for="status" class="form-label">Order Status:</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="Pending" <?php echo $order['OrderStatus'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="Preparing" <?php echo $order['OrderStatus'] === 'Preparing' ? 'selected' : ''; ?>>Preparing</option>
                                            <option value="Ready" <?php echo $order['OrderStatus'] === 'Ready' ? 'selected' : ''; ?>>Ready</option>
                                            <option value="Delivered" <?php echo $order['OrderStatus'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="col-md-4">
                            <!-- Customer Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-user"></i> Customer Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1">
                                        <strong>Name:</strong><br>
                                        <?php
                                        $customerName = trim($order['FirstName'] . ' ' . $order['LastName']);
                                        echo htmlspecialchars($customerName ?: $order['Username']);
                                        ?>
                                    </p>
                                    <?php if ($order['Email']): ?>
                                        <p class="mb-1">
                                            <strong>Email:</strong><br>
                                            <a href="mailto:<?php echo htmlspecialchars($order['Email']); ?>">
                                                <?php echo htmlspecialchars($order['Email']); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($order['PhoneNumber']): ?>
                                        <p class="mb-0">
                                            <strong>Phone:</strong><br>
                                            <a href="tel:<?php echo htmlspecialchars($order['PhoneNumber']); ?>">
                                                <?php echo htmlspecialchars($order['PhoneNumber']); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Order Information -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-info-circle"></i> Order Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-1">
                                        <strong>Order Date:</strong><br>
                                        <?php 
                                        $orderDate = new DateTime($order['OrderDate']);
                                        echo $orderDate->format('F d, Y h:i A');
                                        ?>
                                    </p>
                                    <p class="mb-0">
                                        <strong>Last Modified:</strong><br>
                                        <?php 
                                        if ($order['LastModified']) {
                                            $modifiedDate = new DateTime($order['LastModified']);
                                            echo $modifiedDate->format('F d, Y h:i A');
                                        } else {
                                            echo 'Not modified';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Add Menu Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" 
                               class="form-control" 
                               id="itemSearch" 
                               placeholder="Search items..."
                               onkeyup="filterItems()">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="menuItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($menuItemsByCategory as $category => $items): ?>
                                    <tr class="table-secondary">
                                        <td colspan="4"><strong><?php echo htmlspecialchars($category); ?></strong></td>
                                    </tr>
                                    <?php foreach ($items as $item): ?>
                                        <tr class="menu-item-row">
                                            <td>
                                                <?php echo htmlspecialchars($item['ItemName']); ?>
                                                <?php if ($item['Description']): ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo htmlspecialchars($item['Description']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($category); ?></td>
                                            <td>$<?php echo number_format($item['Price'], 2); ?></td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm"
                                                        onclick='addMenuItem(<?php 
                                                            echo json_encode([
                                                                'id' => $item['ItemID'],
                                                                'name' => htmlspecialchars($item['ItemName']),
                                                                'category' => htmlspecialchars($category),
                                                                'price' => (float)$item['Price'],
                                                                'description' => htmlspecialchars($item['Description'] ?? '')
                                                            ], JSON_HEX_APOS | JSON_HEX_QUOT); 
                                                        ?>)'>
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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
        // Function to update item total and order total
        function updateTotal(input) {
            const row = input.closest('tr');
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const total = quantity * price;
            
            row.querySelector('.item-total').textContent = '$' + total.toFixed(2);
            updateOrderTotal();
        }

        // Function to update order total
        function updateOrderTotal() {
            let total = 0;
            document.querySelectorAll('.item-total').forEach(cell => {
                total += parseFloat(cell.textContent.replace('$', '')) || 0;
            });
            document.getElementById('orderTotal').innerHTML = '<strong>$' + total.toFixed(2) + '</strong>';
        }

        // Function to remove item from order
        function removeItem(button) {
            if (confirm('Are you sure you want to remove this item?')) {
                const row = button.closest('tr');
                row.remove();
                updateOrderTotal();
            }
        }

        // Function to safely escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Function to add menu item to order
        function addMenuItem(item) {
            try {
                const table = document.getElementById('orderItemsTable').getElementsByTagName('tbody')[0];
                const existingRow = table.querySelector(`tr[data-item-id="${item.id}"]`);

                if (existingRow) {
                    const quantityInput = existingRow.querySelector('.quantity-input');
                    quantityInput.value = parseInt(quantityInput.value) + 1;
                    updateTotal(quantityInput);
                } else {
                    const newRow = document.createElement('tr');
                    newRow.className = 'menu-item-row';
                    newRow.setAttribute('data-item-id', item.id);
                    
                    const description = item.description ? `<br><small class="text-muted">${escapeHtml(item.description)}</small>` : '';
                    
                    newRow.innerHTML = `
                        <td>
                            ${escapeHtml(item.name)}
                            ${description}
                        </td>
                        <td>${escapeHtml(item.category)}</td>
                        <td>
                            <input type="number" 
                                   class="form-control quantity-input" 
                                   name="quantity[${item.id}]" 
                                   value="1"
                                   min="1"
                                   required
                                   onchange="updateTotal(this)">
                        </td>
                        <td>
                            <input type="number" 
                                   class="form-control price-input" 
                                   name="price[${item.id}]" 
                                   value="${item.price.toFixed(2)}"
                                   step="0.01"
                                   min="0"
                                   required
                                   onchange="updateTotal(this)">
                        </td>
                        <td class="item-total">$${item.price.toFixed(2)}</td>
                        <td>
                            <button type="button" 
                                    class="btn btn-danger btn-sm"
                                    onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    `;
                    
                    table.appendChild(newRow);
                    updateOrderTotal();
                }

                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addItemModal'));
                if (modal) {
                    modal.hide();
                }
            } catch (error) {
                console.error('Error adding menu item:', error);
                alert('An error occurred while adding the item. Please try again.');
            }
        }

        // Function to filter items in the add item modal
        function filterItems() {
            const searchText = document.getElementById('itemSearch').value.toLowerCase();
            const rows = document.getElementById('menuItemsTable').getElementsByTagName('tr');
            
            let categoryVisible = false;
            let currentCategory = null;
            
            for (let row of rows) {
                if (row.classList.contains('table-secondary')) {
                    // This is a category row
                    if (currentCategory) {
                        currentCategory.style.display = categoryVisible ? '' : 'none';
                    }
                    currentCategory = row;
                    categoryVisible = false;
                } else if (!row.classList.contains('d-none')) {
                    // This is an item row
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchText)) {
                        row.style.display = '';
                        categoryVisible = true;
                    } else {
                        row.style.display = 'none';
                    }
                }
            }
            
            // Handle the last category
            if (currentCategory) {
                currentCategory.style.display = categoryVisible ? '' : 'none';
            }
        }

        // Initialize Bootstrap components
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltips.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Form validation
            const form = document.getElementById('editOrderForm');
            if (form) {
                form.addEventListener('submit', function(event) {
                    const items = document.querySelectorAll('.menu-item-row');
                    if (items.length === 0) {
                        event.preventDefault();
                        alert('Order must have at least one item');
                    }
                });
            }

            // Initialize search input
            const searchInput = document.getElementById('itemSearch');
            if (searchInput) {
                searchInput.focus();
            }
        });
    </script>
</body>
</html>