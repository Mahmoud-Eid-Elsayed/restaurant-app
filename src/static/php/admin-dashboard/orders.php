<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = null;
$orders = [];

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search_query = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

try {
    // Build the query with filters
    $query = "
        SELECT 
            o.*,
            u.Username,
            u.FirstName,
            u.LastName,
            u.Email,
            u.PhoneNumber,
            COUNT(DISTINCT oi.OrderItemID) as unique_items,
            COALESCE(SUM(oi.Quantity), 0) as total_items,
            GROUP_CONCAT(
                CONCAT(mi.ItemName, ' (', oi.Quantity, ')')
                ORDER BY mi.ItemName
                SEPARATOR ', '
            ) as order_details
        FROM `Order` o
        INNER JOIN User u ON o.CustomerID = u.UserID
        LEFT JOIN OrderItem oi ON o.OrderID = oi.OrderID
        LEFT JOIN MenuItem mi ON oi.ItemID = mi.ItemID
        WHERE 1=1
    ";

    $params = [];

    // Apply status filter
    if ($status_filter !== 'all') {
        $query .= " AND o.OrderStatus = ?";
        $params[] = $status_filter;
    }

    // Apply search filter
    if ($search_query) {
        $query .= " AND (
            o.OrderID LIKE ? OR 
            u.Username LIKE ? OR 
            u.FirstName LIKE ? OR 
            u.LastName LIKE ? OR
            u.Email LIKE ? OR
            u.PhoneNumber LIKE ?
        )";
        $search_term = "%$search_query%";
        $params = array_merge($params, array_fill(0, 6, $search_term));
    }

    // Apply date range filter
    if ($date_from) {
        $query .= " AND o.OrderDate >= ?";
        $params[] = $date_from . ' 00:00:00';
    }
    if ($date_to) {
        $query .= " AND o.OrderDate <= ?";
        $params[] = $date_to . ' 23:59:59';
    }

    $query .= "
        GROUP BY o.OrderID, u.Username, u.FirstName, u.LastName, u.Email, u.PhoneNumber
        ORDER BY o.OrderDate DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get order status counts for the filter buttons
    $statusCounts = $conn->query("
        SELECT 
            OrderStatus,
            COUNT(*) as count
    FROM `Order` 
        GROUP BY OrderStatus
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    error_log("Error in orders.php: " . $e->getMessage());
}

// Define status colors for consistency
$statusColors = [
    'Pending' => 'warning',
    'Processing' => 'info',
    'Completed' => 'success',
    'Cancelled' => 'danger',
    'Refunded' => 'secondary'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - ELCHEF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
    <style>
        .order-details-tooltip {
            max-width: 300px;
            white-space: normal;
        }

        .status-badge {
            min-width: 100px;
            text-align: center;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
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
                <li class="active"><a href="orders.php"><i class="fas fa-tags"></i> Orders</a></li>
                <li><a href="special_offers.php"><i class="fas fa-tags"></i> Special Offers</a></li>
                <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
                <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li><a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a></li>
                <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a>

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
                    <h2><i class="fas fa-shopping-cart me-3"></i>Manage Orders</h2>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search"
                                    value="<?php echo htmlspecialchars($search_query); ?>"
                                    placeholder="Search orders...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" name="date_from"
                                    value="<?php echo $date_from; ?>" placeholder="From date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" name="date_to" value="<?php echo $date_to; ?>"
                                    placeholder="To date">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Apply
                            </button>
                        </div>
                    </form>

                    <!-- Status Filter Buttons -->
                    <div class="mt-3">
                        <div class="btn-group">
                            <a href="?status=all"
                                class="btn btn-outline-secondary <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                                All (<?php echo array_sum($statusCounts); ?>)
                            </a>
                            <?php foreach ($statusColors as $status => $color): ?>
                                <a href="?status=<?php echo $status; ?>"
                                    class="btn btn-outline-<?php echo $color; ?> <?php echo $status_filter === $status ? 'active' : ''; ?>">
                                    <?php echo $status; ?> (<?php echo $statusCounts[$status] ?? 0; ?>)
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($orders)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Order Date</th>
                                    <th>Items</th>
                                    <th>Total Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
                                        <td>
                                            <?php
                                            $customerName = trim($order['FirstName'] . ' ' . $order['LastName']);
                                            echo htmlspecialchars($customerName ?: $order['Username']);
                                            ?>
                                        </td>
                                        <td>
                                            <small>
                                                <?php if ($order['Email']): ?>
                                                    <div><i class="fas fa-envelope"></i>
                                                        <?php echo htmlspecialchars($order['Email']); ?></div>
                                                <?php endif; ?>
                                                <?php if ($order['PhoneNumber']): ?>
                                                    <div><i class="fas fa-phone"></i>
                                                        <?php echo htmlspecialchars($order['PhoneNumber']); ?></div>
                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php
                                            $orderDate = new DateTime($order['OrderDate']);
                                            echo $orderDate->format('M d, Y H:i');
                                            ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-custom-class="order-details-tooltip"
                                                title="<?php echo htmlspecialchars($order['order_details']); ?>">
                                                <?php echo (int) $order['total_items']; ?> items
                                                (<?php echo (int) $order['unique_items']; ?> unique)
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">
                                                $<?php echo number_format($order['TotalAmount'], 2); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge status-badge bg-<?php
                                            echo $statusColors[$order['OrderStatus']] ?? 'secondary';
                                            ?>">
                                                <?php echo htmlspecialchars($order['OrderStatus']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="view_order.php?id=<?php echo $order['OrderID']; ?>"
                                                    class="btn btn-info btn-sm" title="View Order Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($order['OrderStatus'] !== 'Completed' && $order['OrderStatus'] !== 'Cancelled'): ?>
                                                    <a href="edit_order.php?id=<?php echo $order['OrderID']; ?>"
                                                        class="btn btn-warning btn-sm" title="Edit Order">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button"
                                                        onclick="showCancelModal(<?php echo $order['OrderID']; ?>, '<?php echo htmlspecialchars(addslashes($order['OrderID'])); ?>')"
                                                        class="btn btn-danger btn-sm" title="Cancel Order">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                                <?php if ($order['OrderStatus'] === 'Completed'): ?>
                                                    <button type="button"
                                                        onclick="showRefundModal(<?php echo $order['OrderID']; ?>, '<?php echo htmlspecialchars(addslashes($order['OrderID'])); ?>')"
                                                        class="btn btn-secondary btn-sm" title="Refund Order">
                                                        <i class="fas fa-undo"></i>
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
                        No orders found matching your criteria.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cancel Order Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Confirm Order Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel Order #<span id="orderNumber"></span>?
                    <p class="text-danger mt-2 mb-0">
                        <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Order</button>
                    <button type="button" class="btn btn-danger" id="confirmCancel">
                        <i class="fas fa-times"></i> Cancel Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Order Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundModalLabel">Confirm Order Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to refund Order #<span id="refundOrderNumber"></span>?</p>
                    <div class="mb-3">
                        <label for="refundReason" class="form-label">Refund Reason</label>
                        <textarea class="form-control" id="refundReason" rows="3" required></textarea>
                    </div>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning" id="confirmRefund">
                        <i class="fas fa-undo"></i> Process Refund
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
    <script>
        let cancelModal, refundModal;
        let orderToCancel = null;
        let orderToRefund = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Initialize modals
            cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
            refundModal = new bootstrap.Modal(document.getElementById('refundModal'));

            // Set up cancel confirmation button
            document.getElementById('confirmCancel').addEventListener('click', function () {
                if (orderToCancel) {
                    window.location.href = `cancel_order.php?id=${orderToCancel.id}&token=${Date.now()}`;
                }
                cancelModal.hide();
            });

            // Set up refund confirmation button
            document.getElementById('confirmRefund').addEventListener('click', function () {
                if (orderToRefund) {
                    const reason = document.getElementById('refundReason').value.trim();
                    if (!reason) {
                        alert('Please provide a reason for the refund');
                        return;
                    }
                    window.location.href = `refund_order.php?id=${orderToRefund.id}&reason=${encodeURIComponent(reason)}&token=${Date.now()}`;
                }
                refundModal.hide();
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

        function showCancelModal(orderId, orderNumber) {
            orderToCancel = {
                id: orderId,
                number: orderNumber
            };
            document.getElementById('orderNumber').textContent = orderNumber;
            cancelModal.show();
        }

        function showRefundModal(orderId, orderNumber) {
            orderToRefund = {
                id: orderId,
                number: orderNumber
            };
            document.getElementById('refundOrderNumber').textContent = orderNumber;
            document.getElementById('refundReason').value = '';
            refundModal.show();
        }
    </script>
</body>

</html>