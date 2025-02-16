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
</head>

<body>
  <div class="wrapper">
    <!-- Include the same sidebar and header as in index.php -->
    <div id="content">
      <div class="header">
        <!-- Header content -->
      </div>
      <div class="main-content">
        <h2>Manage Inventory</h2>
        <a href="add_inventory_item.php" class="btn btn-primary mb-3">Add New Item</a>
        <table class="table table-bordered">
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
                <td><?php echo $item['InventoryItemID']; ?></td>
                <td><?php echo $item['ItemName']; ?></td>
                <td><?php echo $item['SupplierName']; ?></td>
                <td><?php echo $item['QuantityInStock']; ?></td>
                <td><?php echo $item['UnitOfMeasurement']; ?></td>
                <td><?php echo $item['ReorderLevel']; ?></td>
                <td>
                  <a href="edit_inventory_item.php?id=<?php echo $item['InventoryItemID']; ?>"
                    class="btn btn-warning btn-sm">Edit</a>
                  <a href="delete_inventory_item.php?id=<?php echo $item['InventoryItemID']; ?>"
                    class="btn btn-danger btn-sm">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>