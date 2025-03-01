<?php
require_once '../../connection/db.php';

// Handle form submission for adding/editing a supplier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['add_supplier'])) {
    // Add a new supplier
    $supplierName = $_POST['supplier_name'];
    $contactPerson = $_POST['contact_person'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    try {
      $stmt = $conn->prepare("INSERT INTO Supplier (SupplierName, ContactPerson, Email, Phone, Address) VALUES (:supplierName, :contactPerson, :email, :phone, :address)");
      $stmt->execute([
        ':supplierName' => $supplierName,
        ':contactPerson' => $contactPerson,
        ':email' => $email,
        ':phone' => $phone,
        ':address' => $address
      ]);
    } catch (PDOException $e) {
      die("Error adding supplier: " . $e->getMessage());
    }
  } elseif (isset($_POST['add_order'])) {
    // Add a new order for a supplier
    $supplierID = $_POST['supplier_id'];
    $orderDate = $_POST['order_date'];
    $totalAmount = $_POST['total_amount'];
    $status = $_POST['status'];

    try {
      $stmt = $conn->prepare("INSERT INTO SupplierOrders (SupplierID, OrderDate, TotalAmount, Status) VALUES (:supplierID, :orderDate, :totalAmount, :status)");
      $stmt->execute([
        ':supplierID' => $supplierID,
        ':orderDate' => $orderDate,
        ':totalAmount' => $totalAmount,
        ':status' => $status
      ]);
    } catch (PDOException $e) {
      die("Error adding order: " . $e->getMessage());
    }
  }
}

// Handle delete request for suppliers
if (isset($_GET['delete_id'])) {
  $supplierID = $_GET['delete_id'];

  try {
    // Delete associated inventory items
    $stmt = $conn->prepare("DELETE FROM InventoryItem WHERE SupplierID = :supplierID");
    $stmt->execute([':supplierID' => $supplierID]);

    // Delete the supplier
    $stmt = $conn->prepare("DELETE FROM Supplier WHERE SupplierID = :supplierID");
    $stmt->execute([':supplierID' => $supplierID]);

    header("Location: suppliers.php");
    exit();
  } catch (PDOException $e) {
    die("Error deleting supplier: " . $e->getMessage());
  }
}

// Fetch all suppliers
try {
  $suppliers = $conn->query("SELECT * FROM Supplier")->fetchAll();
} catch (PDOException $e) {
  die("Error fetching suppliers: " . $e->getMessage());
}

// Fetch all orders for each supplier
$supplierOrders = [];
foreach ($suppliers as $supplier) {
  try {
    $stmt = $conn->prepare("SELECT * FROM SupplierOrders WHERE SupplierID = :supplierID");
    $stmt->execute([':supplierID' => $supplier['SupplierID']]);
    $supplierOrders[$supplier['SupplierID']] = $stmt->fetchAll();
  } catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suppliers - Admin Dashboard</title>
  <link href="../../../../src/assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../../../src/assets/libraries/fontawesome-6.7.2-web/css/all.min.css">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
  <style>
    .reservation-details {
      background-color: #f8f9fa;
      border-radius: 0.25rem;
      padding: 1.5rem;
      margin-bottom: 1rem;
    }

    .status-badge {
      font-size: 1rem;
      padding: 0.5rem 1rem;
    }

    .timeline {
      position: relative;
      padding: 1rem 0;
    }

    .timeline-item {
      position: relative;
      padding-left: 40px;
      margin-bottom: 1.5rem;
    }

    .timeline-item:last-child {
      margin-bottom: 0;
    }

    .timeline-marker {
      position: absolute;
      left: 0;
      top: 0;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background: #f8f9fa;
      border: 2px solid #dee2e6;
      text-align: center;
      line-height: 20px;
    }

    .timeline-marker i {
      font-size: 12px;
    }

    .timeline-content {
      position: relative;
      padding-bottom: 1rem;
      border-bottom: 1px dashed #dee2e6;
    }

    .timeline-item:last-child .timeline-content {
      border-bottom: none;
      padding-bottom: 0;
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

        <div class="container mt-5">
          <h1 class="mb-4">Suppliers</h1>

          <!-- Add Supplier Form -->
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="card-title">Add New Supplier</h5>
            </div>

            <div class="card-body">
              <form method="POST">
                <div class="mb-3">
                  <label for="supplier_name" class="form-label">Supplier Name</label>
                  <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                </div>

                <div class="mb-3">
                  <label for="contact_person" class="form-label">Contact Person</label>
                  <input type="text" class="form-control" id="contact_person" name="contact_person">
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3">
                  <label for="phone" class="form-label">Phone</label>
                  <input type="text" class="form-control" id="phone" name="phone">
                </div>
                <div class="mb-3">
                  <label for="address" class="form-label">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>
                <button type="submit" name="add_supplier" class="btn btn-primary">Add Supplier</button>
              </form>
            </div>
          </div>


          <!-- Suppliers Table -->
          <div class="card mb-4">

            <div class="card-header">
              <h5 class="card-title">Supplier List</h5>
            </div>
            <div class="card-body">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Supplier Name</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($supplier['SupplierID']); ?></td>
                      <td><?php echo htmlspecialchars($supplier['SupplierName']); ?></td>
                      <td><?php echo htmlspecialchars($supplier['ContactPerson']); ?></td>
                      <td><?php echo htmlspecialchars($supplier['Email']); ?></td>
                      <td><?php echo htmlspecialchars($supplier['Phone']); ?></td>
                      <td><?php echo htmlspecialchars($supplier['Address']); ?></td>
                      <td>
                        <a href="edit_supplier.php?id=<?php echo $supplier['SupplierID']; ?>"
                          class="btn btn-sm btn-warning">Edit</a>
                        <a href="suppliers.php?delete_id=<?php echo $supplier['SupplierID']; ?>"
                          class="btn btn-sm btn-danger"
                          onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Supplier Orders Section -->
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Supplier Orders</h5>
            </div>
            <div class="card-body">
              <!-- Add Order Form -->
              <form method="POST" class="mb-4">
                <div class="row">
                  <div class="col-md-3">
                    <label for="supplier_id" class="form-label">Supplier</label>
                    <select class="form-control" id="supplier_id" name="supplier_id" required>
                      <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?php echo $supplier['SupplierID']; ?>">
                          <?php echo htmlspecialchars($supplier['SupplierName']); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-2">
                    <label for="order_date" class="form-label">Order Date</label>
                    <input type="date" class="form-control" id="order_date" name="order_date" required>
                  </div>
                  <div class="col-md-2">
                    <label for="total_amount" class="form-label">Total Amount</label>
                    <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount"
                      required>
                  </div>
                  <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                      <option value="Pending">Pending</option>
                      <option value="Shipped">Shipped</option>
                      <option value="Delivered">Delivered</option>
                    </select>
                  </div>
                  <div class="col-md-3 align-self-end">
                    <button type="submit" name="add_order" class="btn btn-primary">Add Order</button>
                  </div>
                </div>
              </form>

              <!-- Orders Table -->
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Supplier</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($supplierOrders as $supplierID => $orders): ?>
                    <?php foreach ($orders as $order): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
                        <td>
                          <?php echo htmlspecialchars($suppliers[array_search($supplierID, array_column($suppliers, 'SupplierID'))]['SupplierName']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                        <td>$<?php echo htmlspecialchars($order['TotalAmount']); ?></td>
                        <td><?php echo htmlspecialchars($order['Status']); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="../../js/admin-dashboard.js"></script>
</body>

</html>