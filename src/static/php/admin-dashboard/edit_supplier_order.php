<?php
require_once '../../connection/db.php';

// Check if the OrderID is provided in the URL
if (!isset($_GET['id'])) {
    die("Order ID is missing.");
}

$orderID = $_GET['id'];

// Fetch the order details from the database
try {
    $stmt = $conn->prepare("SELECT * FROM SupplierOrders WHERE OrderID = :orderID");
    $stmt->execute([':orderID' => $orderID]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found.");
    }
} catch (PDOException $e) {
    die("Error fetching order: " . $e->getMessage());
}

// Fetch all suppliers for the dropdown
try {
    $suppliers = $conn->query("SELECT * FROM Supplier")->fetchAll();
} catch (PDOException $e) {
    die("Error fetching suppliers: " . $e->getMessage());
}

// Handle form submission for updating the order
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $supplierID = $_POST['supplier_id'];
    $orderDate = $_POST['order_date'];
    $totalAmount = $_POST['total_amount'];
    $status = $_POST['status'];

    try {
        $stmt = $conn->prepare("UPDATE SupplierOrders SET SupplierID = :supplierID, OrderDate = :orderDate, TotalAmount = :totalAmount, Status = :status WHERE OrderID = :orderID");
        $stmt->execute([
            ':supplierID' => $supplierID,
            ':orderDate' => $orderDate,
            ':totalAmount' => $totalAmount,
            ':status' => $status,
            ':orderID' => $orderID
        ]);

        // Redirect back to the suppliers page after updating
        header("Location: suppliers.php");
        exit();
    } catch (PDOException $e) {
        die("Error updating order: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Supplier Order - ELCHEF Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            font-size: 2rem;
            font-weight: 600;
            margin: 0;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            border-color: #3498db;
        }

        .btn-primary {
            background: #3498db;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
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
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
                <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li class="active"><a href="suppliers.php"><i class="fa-solid fa-truck"></i> Suppliers</a></li>
                <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a></li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <button type="button" id="sidebarToggle" class="btn">
                <i class="fas fa-bars"></i>
            </button>

            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h1><i class="fas fa-edit me-2"></i>Edit Supplier Order</h1>
                            <p>Update the details of the supplier order</p>
                        </div>
                    </div>
                </div>

                <!-- Edit Order Form -->
                <div class="card">
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Supplier</label>
                                <select class="form-select" name="supplier_id" required>
                                    <?php foreach ($suppliers as $supplier): ?>
                                        <option value="<?php echo $supplier['SupplierID']; ?>"
                                            <?php echo $order['SupplierID'] == $supplier['SupplierID'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($supplier['SupplierName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Order Date</label>
                                <input type="date" class="form-control" name="order_date"
                                    value="<?php echo htmlspecialchars($order['OrderDate']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Total Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" name="total_amount"
                                        value="<?php echo htmlspecialchars($order['TotalAmount']); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status" required>
                                    <option value="Pending" <?php echo $order['Status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Shipped" <?php echo $order['Status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="Delivered" <?php echo $order['Status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar
        document.getElementById('sidebarToggle').addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>

</html>