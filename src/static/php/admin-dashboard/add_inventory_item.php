<?php
require_once '../../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $reorderLevel = $_POST['reorder_level'];
    $supplierID = $_POST['supplier_id'];

    try {
        $stmt = $conn->prepare("INSERT INTO InventoryItem (ItemName, QuantityInStock, UnitOfMeasurement, ReorderLevel, SupplierID) VALUES (:itemName, :quantity, :unit, :reorderLevel, :supplierID)");
        $stmt->execute([
            ':itemName' => $itemName,
            ':quantity' => $quantity,
            ':unit' => $unit,
            ':reorderLevel' => $reorderLevel,
            ':supplierID' => $supplierID
        ]);

        header("Location: inventory.php?message=Item added successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: inventory.php?error=Error adding item: " . urlencode($e->getMessage()));
        exit();
    }
}

// Fetch all suppliers for the dropdown
$suppliers = $conn->query("SELECT * FROM Supplier")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Inventory Item - ELCHEF</title>
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
                <li><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li class="active"><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
                <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li><a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a></li>
                <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a>

            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <button type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="main-content">
                <div class="inventory-header d-flex justify-content-between align-items-center">
                    <h2><i class="fas fa-box me-2"></i>Add New Inventory Item</h2>
                </div>

                <form method="POST">
                    <div class="mb-3">
                        <label for="item_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="item_name" name="item_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <div class="mb-3">
                        <label for="unit" class="form-label">Unit of Measurement</label>
                        <input type="text" class="form-control" id="unit" name="unit" required>
                    </div>
                    <div class="mb-3">
                        <label for="reorder_level" class="form-label">Reorder Level</label>
                        <input type="number" step="0.01" class="form-control" id="reorder_level" name="reorder_level"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label">Supplier</label>
                        <select class="form-control" id="supplier_id" name="supplier_id" required>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo htmlspecialchars($supplier['SupplierID']); ?>">
                                    <?php echo htmlspecialchars($supplier['SupplierName']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
</body>

</html>