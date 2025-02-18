<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$error = null;
$order = null;
$orderItems = [];
$orderHistory = [];

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orders.php?error=' . urlencode('Invalid order ID'));
    exit;
}

$orderId = (int)$_GET['id'];

try {
    // Fetch order details with customer information
$stmt = $conn->prepare("
        SELECT 
            o.*,
            u.Username,
            u.FirstName,
            u.LastName,
            u.Email,
            u.PhoneNumber,
            u.Address
        FROM `Order` o
        INNER JOIN User u ON o.CustomerID = u.UserID
        WHERE o.OrderID = ?
");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
        throw new Exception('Order not found');
}

    // Fetch order items with menu item details
$stmt = $conn->prepare("
        SELECT 
            oi.*,
            mi.ItemName,
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

    // Fetch order status history
    $stmt = $conn->prepare("
        SELECT *
        FROM OrderStatusHistory
        WHERE OrderID = ?
        ORDER BY ChangedAt DESC
    ");
    $stmt->execute([$orderId]);
    $orderHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Error in view_order.php: " . $e->getMessage());
} catch (PDOException $e) {
    $error = "Database error occurred";
    error_log("Database error in view_order.php: " . $e->getMessage());
}

// Define status colors (matching orders.php)
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
    <title>View Order #<?php echo $orderId; ?> - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
    <style>
        .status-badge {
            min-width: 100px;
            text-align: center;
        }
        .timeline {
            border-left: 3px solid #dee2e6;
            padding-left: 20px;
            margin-left: 10px;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -27px;
            top: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #007bff;
        }
        .order-summary {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            padding: 1rem;
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
                            Order #<?php echo htmlspecialchars($order['OrderID']); ?>
                            <span class="badge status-badge bg-<?php 
                                echo $statusColors[$order['OrderStatus']] ?? 'secondary';
                            ?>">
                                <?php echo htmlspecialchars($order['OrderStatus']); ?>
                            </span>
                        </h2>
                        <div class="btn-group">
                            <a href="orders.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Orders
                            </a>
                            <?php if ($order['OrderStatus'] !== 'Completed' && $order['OrderStatus'] !== 'Cancelled'): ?>
                                <a href="edit_order.php?id=<?php echo $order['OrderID']; ?>" 
                                   class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Order
                                </a>
                                <button type="button"
                                        onclick="showCancelModal(<?php echo $order['OrderID']; ?>, '<?php echo htmlspecialchars(addslashes($order['OrderID'])); ?>')"
                                        class="btn btn-danger">
                                    <i class="fas fa-times"></i> Cancel Order
                                </button>
                            <?php endif; ?>
                            <?php if ($order['OrderStatus'] === 'Completed'): ?>
                                <button type="button"
                                        onclick="showRefundModal(<?php echo $order['OrderID']; ?>, '<?php echo htmlspecialchars(addslashes($order['OrderID'])); ?>')"
                                        class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Refund Order
                                </button>
                            <?php endif; ?>
          </div>
        </div>

                    <div class="row">
                        <!-- Order Details -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-shopping-cart"></i> Order Items</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
            <tr>
                                                    <th>Item</th>
                                                    <th>Category</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
                                                <?php 
                                                $subtotal = 0;
                                                foreach ($orderItems as $item): 
                                                    $itemTotal = $item['Quantity'] * $item['PriceAtTimeOfOrder'];
                                                    $subtotal += $itemTotal;
                                                ?>
              <tr>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($item['ItemName']); ?></strong>
                                                            <?php if ($item['Description']): ?>
                                                                <br>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars($item['Description']); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($item['CategoryName']); ?></td>
                                                        <td class="text-center"><?php echo (int)$item['Quantity']; ?></td>
                                                        <td class="text-end">$<?php echo number_format($item['PriceAtTimeOfOrder'], 2); ?></td>
                                                        <td class="text-end">$<?php echo number_format($itemTotal, 2); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
                                            <tfoot class="table-light">
                                                <tr>
                                                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                                    <td class="text-end">$<?php echo number_format($subtotal, 2); ?></td>
                                                </tr>
                                                <?php if (isset($order['TaxAmount']) && $order['TaxAmount'] > 0): ?>
                                                    <tr>
                                                        <td colspan="4" class="text-end">Tax:</td>
                                                        <td class="text-end">$<?php echo number_format($order['TaxAmount'], 2); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                                    <td class="text-end"><strong>$<?php echo number_format($order['TotalAmount'], 2); ?></strong></td>
                                                </tr>
                                            </tfoot>
        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Timeline -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-history"></i> Order History</h5>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <?php foreach ($orderHistory as $history): ?>
                                            <div class="timeline-item">
                                                <small class="text-muted">
                                                    <?php 
                                                    $historyDate = new DateTime($history['ChangedAt']);
                                                    echo $historyDate->format('M d, Y H:i');
                                                    ?>
                                                </small>
                                                <p class="mb-0">
                                                    Status changed from 
                                                    <span class="badge bg-<?php echo $statusColors[$history['StatusFrom']] ?? 'secondary'; ?>">
                                                        <?php echo htmlspecialchars($history['StatusFrom']); ?>
                                                    </span>
                                                    to
                                                    <span class="badge bg-<?php echo $statusColors[$history['StatusTo']] ?? 'secondary'; ?>">
                                                        <?php echo htmlspecialchars($history['StatusTo']); ?>
                                                    </span>
                                                </p>
                                                <?php if ($history['Notes']): ?>
                                                    <p class="text-muted mb-0"><small><?php echo htmlspecialchars($history['Notes']); ?></small></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="timeline-item">
                                            <small class="text-muted">
                                                <?php 
                                                $orderDate = new DateTime($order['OrderDate']);
                                                echo $orderDate->format('M d, Y H:i');
                                                ?>
                                            </small>
                                            <p class="mb-0">Order placed</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Details -->
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-user"></i> Customer Information</h5>
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
                                        <p class="mb-1">
                                            <strong>Phone:</strong><br>
                                            <a href="tel:<?php echo htmlspecialchars($order['PhoneNumber']); ?>">
                                                <?php echo htmlspecialchars($order['PhoneNumber']); ?>
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                    <?php if ($order['Address']): ?>
                                        <p class="mb-1">
                                            <strong>Address:</strong><br>
                                            <?php echo nl2br(htmlspecialchars($order['Address'])); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="order-summary">
                                        <p class="mb-1">
                                            <strong>Order Date:</strong><br>
                                            <?php echo $orderDate->format('F d, Y h:i A'); ?>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Status:</strong><br>
                                            <span class="badge status-badge bg-<?php 
                                                echo $statusColors[$order['OrderStatus']] ?? 'secondary';
                                            ?>">
                                                <?php echo htmlspecialchars($order['OrderStatus']); ?>
                                            </span>
                                        </p>
                                        <p class="mb-1">
                                            <strong>Total Items:</strong><br>
                                            <?php 
                                            $totalItems = array_sum(array_column($orderItems, 'Quantity'));
                                            echo $totalItems . ' items';
                                            ?>
                                        </p>
                                        <p class="mb-0">
                                            <strong>Total Amount:</strong><br>
                                            <span class="h4">$<?php echo number_format($order['TotalAmount'], 2); ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modals
            cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
            refundModal = new bootstrap.Modal(document.getElementById('refundModal'));
            
            // Set up cancel confirmation button
            document.getElementById('confirmCancel').addEventListener('click', function() {
                if (orderToCancel) {
                    window.location.href = `cancel_order.php?id=${orderToCancel.id}&token=${Date.now()}`;
                }
                cancelModal.hide();
            });

            // Set up refund confirmation button
            document.getElementById('confirmRefund').addEventListener('click', function() {
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