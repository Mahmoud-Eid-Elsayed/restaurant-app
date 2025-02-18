<?php
require_once '../../connection/db.php';

// Fetch all inventory items with supplier names
$stmt = $conn->query("
    SELECT InventoryItem.*, Supplier.SupplierName 
    FROM InventoryItem 
    INNER JOIN Supplier ON InventoryItem.SupplierID = Supplier.SupplierID
");
$inventoryItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Inventory - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
  <style>
    .inventory-header {
      background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
      color: white;
      padding: 2rem;
      border-radius: 1rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .inventory-header h2 {
      margin: 0;
      color: white;
    }
    .table-responsive {
      background: white;
      border-radius: 0.5rem;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      padding: 1rem;
    }
    .table th {
      white-space: nowrap;
      background: #f8f9fa;
    }
    .table td {
      vertical-align: middle;
    }
    .status-badge {
      padding: 0.5rem 1rem;
      border-radius: 2rem;
      font-weight: 500;
    }
    .btn-group-sm > .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.875rem;
      border-radius: 0.2rem;
    }
    .low-stock {
      color: #dc3545;
      font-weight: 500;
    }
    @media (max-width: 768px) {
      .inventory-header {
        padding: 1.5rem;
        margin-bottom: 1.5rem;
      }
      .table-responsive {
        padding: 0.5rem;
      }
      .btn-group-sm > .btn {
        padding: 0.375rem 0.75rem;
      }
      .mobile-stack {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
      }
      .table td {
        white-space: normal;
        min-width: 100px;
      }
      .table td:last-child {
        min-width: 120px;
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
        <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
        <li class="active"><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
      </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
      <button type="button" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>

      <div class="main-content">
        <div class="inventory-header d-flex justify-content-between align-items-center">
          <h2><i class="fas fa-box me-2"></i>Manage Inventory</h2>
          <a href="add_inventory_item.php" class="btn btn-light">
            <i class="fas fa-plus me-2"></i>Add New Item
          </a>
        </div>

        <?php if (isset($_GET['message'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <?php if (!empty($inventoryItems)): ?>
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Item Name</th>
                  <th>Supplier</th>
                  <th>Quantity</th>
                  <th>Unit</th>
                  <th>Reorder Level</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($inventoryItems as $item): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($item['InventoryItemID']); ?></td>
                    <td>
                      <strong><?php echo htmlspecialchars($item['ItemName']); ?></strong>
                      <?php if ($item['QuantityInStock'] <= $item['ReorderLevel']): ?>
                        <span class="badge bg-danger ms-2">Low Stock</span>
                      <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['SupplierName']); ?></td>
                    <td class="<?php echo $item['QuantityInStock'] <= $item['ReorderLevel'] ? 'low-stock' : ''; ?>">
                      <?php echo htmlspecialchars($item['QuantityInStock']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($item['UnitOfMeasurement']); ?></td>
                    <td><?php echo htmlspecialchars($item['ReorderLevel']); ?></td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <a href="edit_inventory_item.php?id=<?php echo $item['InventoryItemID']; ?>" 
                           class="btn btn-warning" title="Edit Item">
                          <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" 
                                onclick="confirmDelete(<?php echo $item['InventoryItemID']; ?>, '<?php echo htmlspecialchars(addslashes($item['ItemName'])); ?>')"
                                class="btn btn-danger" title="Delete Item">
                          <i class="fas fa-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No inventory items found. Click "Add New Item" to add one.
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
          Are you sure you want to delete the inventory item "<span id="itemName"></span>"?
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

    document.addEventListener('DOMContentLoaded', function() {
      deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
      
      document.getElementById('confirmDelete').addEventListener('click', function() {
        if (itemToDelete) {
          window.location.href = `delete_inventory_item.php?id=${itemToDelete.id}&token=${Date.now()}`;
        }
        deleteModal.hide();
      });

      // Auto-close alerts after 5 seconds
      setTimeout(function() {
        document.querySelectorAll('.alert').forEach(function(alert) {
          if (alert && typeof bootstrap !== 'undefined') {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
          }
        });
      }, 5000);
    });

    function confirmDelete(itemId, itemName) {
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