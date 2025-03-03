<?php
require_once '../../connection/db.php';

// Handle form submission for adding/editing a supplier
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['add_supplier'])) {
    // Add a new supplier
    $supplierName = $_POST['supplier_name'];
    $contactPerson = $_POST['contact_person'];
    $email = $_POST['email'];
    $PhoneNumber = $_POST['PhoneNumber'];
    $address = $_POST['address'];

    try {
      $stmt = $conn->prepare("INSERT INTO supplier (SupplierName, ContactPerson, Email, PhoneNumber, Address) VALUES (:supplierName, :contactPerson, :email, :PhoneNumber, :address)");
      $stmt->execute([
        ':supplierName' => $supplierName,
        ':contactPerson' => $contactPerson,
        ':email' => $email,
        ':PhoneNumber' => $PhoneNumber,
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
  <title>Suppliers Management - ELCHEF Admin</title>
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

    .page-header p {
      margin: 0.5rem 0 0;
      opacity: 0.9;
    }

    .stats-card {
      background: white;
      border-radius: 12px;
      padding: 1.5rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
    }

    .stats-card:hover {
      transform: translateY(-5px);
    }

    .stats-card .icon {
      width: 50px;
      height: 50px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }

    .stats-card .number {
      font-size: 1.8rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .stats-card .label {
      color: #6c757d;
      font-size: 0.9rem;
    }

    .action-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
      margin-bottom: 2rem;
    }

    .action-card .card-header {
      background: none;
      padding: 1.5rem;
      border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .action-card .card-header h5 {
      margin: 0;
      font-weight: 600;
      color: #2c3e50;
    }

    .action-card .card-body {
      padding: 1.5rem;
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

    .table {
      border-radius: 8px;
      overflow: hidden;
    }

    .table th {
      background: #f8f9fa;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
      padding: 1rem;
    }

    .table td {
      padding: 1rem;
      vertical-align: middle;
    }

    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-size: 0.85rem;
      font-weight: 500;
    }

    .status-pending {
      background: rgba(255, 193, 7, 0.1);
      color: #ffc107;
    }

    .status-shipped {
      background: rgba(52, 152, 219, 0.1);
      color: #3498db;
    }

    .status-delivered {
      background: rgba(40, 167, 69, 0.1);
      color: #28a745;
    }

    .btn-action {
      padding: 0.5rem;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .btn-action:hover {
      transform: translateY(-2px);
    }

    .order-form {
      background: #f8f9fa;
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
      .page-header {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .stats-card {
        margin-bottom: 1rem;
      }

      .action-card {
        margin-bottom: 1.5rem;
      }
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
        <li><a href="special_offers.php"><i class="fas fa-tags"></i> Special Offers</a></li>
        <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
        <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
        <li class="active"><a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a></li>
        <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a>

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
              <h1><i class="fas fa-truck me-2"></i>Suppliers Management</h1>
              <p>Manage your suppliers and their orders</p>
            </div>
            <div class="col-md-6 text-md-end">
              <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="fas fa-plus me-2"></i>Add New Supplier
              </button>
            </div>
          </div>
        </div>

        <!-- Statistics Row -->
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="stats-card">
              <div class="icon bg-primary bg-opacity-10 text-primary">
                <i class="fas fa-users"></i>
              </div>
              <div class="number text-primary"><?php echo count($suppliers); ?></div>
              <div class="label">Total Suppliers</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stats-card">
              <div class="icon bg-success bg-opacity-10 text-success">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <div class="number text-success">
                <?php
                $totalOrders = 0;
                foreach ($supplierOrders as $orders) {
                  $totalOrders += count($orders);
                }
                echo $totalOrders;
                ?>
              </div>
              <div class="label">Total Orders</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="stats-card">
              <div class="icon bg-warning bg-opacity-10 text-warning">
                <i class="fas fa-clock"></i>
              </div>
              <div class="number text-warning">
                <?php
                $pendingOrders = 0;
                foreach ($supplierOrders as $orders) {
                  foreach ($orders as $order) {
                    if ($order['Status'] === 'Pending') {
                      $pendingOrders++;
                    }
                  }
                }
                echo $pendingOrders;
                ?>
              </div>
              <div class="label">Pending Orders</div>
            </div>
          </div>
        </div>

        <!-- Suppliers List -->
        <div class="action-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-users me-2"></i>Supplier List</h5>
            <div class="form-group mb-0">
              <input type="text" class="form-control" id="supplierSearch" placeholder="Search suppliers...">
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
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
                      <td>#<?php echo htmlspecialchars($supplier['SupplierID']); ?></td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="avatar-sm bg-primary bg-opacity-10 text-primary rounded-circle me-2">
                            <?php echo strtoupper(substr($supplier['SupplierName'], 0, 1)); ?>
                          </div>
                          <div>
                            <div class="fw-semibold"><?php echo htmlspecialchars($supplier['SupplierName']); ?></div>
                          </div>
                        </div>
                      </td>
                      <td><?php echo htmlspecialchars($supplier['ContactPerson']); ?></td>
                      <td>
                        <a href="mailto:<?php echo htmlspecialchars($supplier['Email']); ?>" class="text-decoration-none">
                          <?php echo htmlspecialchars($supplier['Email']); ?>
                        </a>
                      </td>
                      <td>
                        <a href="tel:<?php echo htmlspecialchars($supplier['PhoneNumber']); ?>"
                          class="text-decoration-none">
                          <?php echo htmlspecialchars($supplier['PhoneNumber']); ?>
                        </a>
                      </td>
                      <td><?php echo htmlspecialchars($supplier['Address']); ?></td>
                      <td>
                        <div class="btn-group">
                          <button type="button" class="btn btn-sm btn-outline-primary btn-action"
                            onclick="editSupplier(<?php echo $supplier['SupplierID']; ?>)">
                            <i class="fas fa-edit"></i>
                          </button>
                          <button type="button" class="btn btn-sm btn-outline-danger btn-action"
                            onclick="deleteSupplier(<?php echo $supplier['SupplierID']; ?>, '<?php echo htmlspecialchars($supplier['SupplierName']); ?>')">
                            <i class="fas fa-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Supplier Orders -->
        <div class="action-card">
          <div class="card-header">
            <h5><i class="fas fa-shopping-cart me-2"></i>Supplier Orders</h5>
          </div>
          <div class="card-body">
            <!-- Add Order Form -->
            <form method="POST" class="order-form mb-4">
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Supplier</label>
                  <select class="form-select" name="supplier_id" required>
                    <?php foreach ($suppliers as $supplier): ?>
                      <option value="<?php echo $supplier['SupplierID']; ?>">
                        <?php echo htmlspecialchars($supplier['SupplierName']); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Order Date</label>
                  <input type="date" class="form-control" name="order_date" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Total Amount</label>
                  <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" step="0.01" class="form-control" name="total_amount" required>
                  </div>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Status</label>
                  <select class="form-select" name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="Shipped">Shipped</option>
                    <option value="Delivered">Delivered</option>
                  </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                  <button type="submit" name="add_order" class="btn btn-primary w-100">
                    <i class="fas fa-plus me-2"></i>Add Order
                  </button>
                </div>
              </div>
            </form>

            <!-- Orders Table -->
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Supplier</th>
                    <th>Order Date</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($supplierOrders as $supplierID => $orders): ?>
                    <?php foreach ($orders as $order): ?>
                      <tr>
                        <td>#<?php echo htmlspecialchars($order['OrderID']); ?></td>
                        <td>
                          <?php
                          $supplierName = '';
                          foreach ($suppliers as $supplier) {
                            if ($supplier['SupplierID'] == $supplierID) {
                              $supplierName = $supplier['SupplierName'];
                              break;
                            }
                          }
                          echo htmlspecialchars($supplierName);
                          ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                        <td>$<?php echo number_format($order['TotalAmount'], 2); ?></td>
                        <td>
                          <span class="status-badge status-<?php echo strtolower($order['Status']); ?>">
                            <?php echo htmlspecialchars($order['Status']); ?>
                          </span>
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-action"
                              onclick="editOrder(<?php echo $order['OrderID']; ?>)">
                              <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success btn-action"
                              onclick="updateStatus(<?php echo $order['OrderID']; ?>)">
                              <i class="fas fa-sync-alt"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Supplier Modal -->
  <div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add New Supplier</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Supplier Name</label>
              <input type="text" class="form-control" name="supplier_name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Contact Person</label>
              <input type="text" class="form-control" name="contact_person">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email">
            </div>
            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="PhoneNumber">
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_supplier" class="btn btn-primary">Add Supplier</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle sidebar
    document.getElementById('sidebarToggle').addEventListener('click', function () {
      document.getElementById('sidebar').classList.toggle('active');
    });

    // Search functionality
    document.getElementById('supplierSearch').addEventListener('keyup', function () {
      const searchText = this.value.toLowerCase();
      const tableRows = document.querySelectorAll('tbody tr');

      tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
      });
    });

    // Delete supplier confirmation
    function deleteSupplier(id, name) {
      if (confirm(`Are you sure you want to delete supplier "${name}"?`)) {
        window.location.href = `suppliers.php?delete_id=${id}`;
      }
    }

    // Edit supplier
    function editSupplier(id) {
      window.location.href = `edit_supplier.php?id=${id}`;
    }

    // Edit order
    function editOrder(id) {
      window.location.href = `edit_supplier_order.php?id=${id}`;
    }

    // Update order status
    function updateStatus(id) {
      // Create a Bootstrap modal for status selection
      const modalHTML = `
        <div class="modal fade" id="statusModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Order Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label for="statusSelect" class="form-label">Select New Status:</label>
                        <select id="statusSelect" class="form-select">
                            <option value="Pending">Pending</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Delivered">Delivered</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Update</button>
                    </div>
                </div>
            </div>
        </div>
    `;

      // Append the modal to the body
      document.body.insertAdjacentHTML('beforeend', modalHTML);

      // Show the modal
      const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
      statusModal.show();

      // Handle the "Update" button click
      document.getElementById('confirmStatusUpdate').addEventListener('click', () => {
        const newStatus = document.getElementById('statusSelect').value;

        // Send the update request
        fetch('update_order_status.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            order_id: id,
            status: newStatus
          })
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Show a success toast
              showToast('Status updated successfully!', 'success');

              // Update the status in the table row dynamically
              const statusBadge = document.querySelector(`tr[data-order-id="${id}"] .status-badge`);
              if (statusBadge) {
                statusBadge.textContent = newStatus;
                statusBadge.className = `status-badge status-${newStatus.toLowerCase()}`;
              }
            } else {
              // Show an error toast
              showToast('Error updating status: ' + data.error, 'danger');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while updating the status', 'danger');
          })
          .finally(() => {
            // Hide the modal
            statusModal.hide();
            document.getElementById('statusModal').remove(); // Clean up the modal
          });
      });
    }

    // Function to show a toast notification
    function showToast(message, type = 'success') {
      const toastContainer = document.getElementById('toastContainer');
      if (!toastContainer) {
        // Create a toast container if it doesn't exist
        const containerHTML = `<div id="toastContainer" aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3" style="z-index: 11"></div>`;
        document.body.insertAdjacentHTML('beforeend', containerHTML);
      }

      const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

      // Append the toast to the container
      toastContainer.insertAdjacentHTML('beforeend', toastHTML);

      // Initialize and show the toast
      const toastElement = toastContainer.lastElementChild;
      const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 3000
      });
      toast.show();

      // Remove the toast after it hides
      toastElement.addEventListener('hidden.bs.toast', () => {
        toastElement.remove();
      });
    }
    // Auto-hide alerts after 5 seconds
    setTimeout(function () {
      const alerts = document.querySelectorAll('.alert');
      alerts.forEach(alert => {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
      });
    }, 5000);
  </script>
  <!-- Toast Container -->
  <div id="toastContainer" aria-live="polite" aria-atomic="true" class="position-fixed bottom-0 end-0 p-3"
    style="z-index: 11"></div>
</body>

</html>