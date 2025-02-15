<?php
require_once '../../connection/db.php';

// Fetch all suppliers
$stmt = $conn->query("SELECT * FROM Supplier");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $itemName = $_POST['itemName'];
  $quantity = $_POST['quantity'];
  $unit = $_POST['unit'];
  $reorderLevel = $_POST['reorderLevel'];
  $supplierID = $_POST['supplierID'];

  $stmt = $conn->prepare("
        INSERT INTO InventoryItem (ItemName, QuantityInStock, UnitOfMeasurement, ReorderLevel, SupplierID)
        VALUES (:itemName, :quantity, :unit, :reorderLevel, :supplierID)
    ");
  $stmt->execute([
    ':itemName' => $itemName,
    ':quantity' => $quantity,
    ':unit' => $unit,
    ':reorderLevel' => $reorderLevel,
    ':supplierID' => $supplierID
  ]);

  header("Location: ../inventory.php");
  exit();
}
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
    <!-- Include the same sidebar and header as in index.php -->
    <div id="content">
      <div class="header">
        <!-- Header content -->
      </div>
      <div class="main-content">
        <h2>Add Inventory Item</h2>
        <form method="POST">
          <div class="mb-3">
            <label for="itemName" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="itemName" name="itemName" required>
          </div>
          <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
          </div>
          <div class="mb-3">
            <label for="unit" class="form-label">Unit of Measurement</label>
            <input type="text" class="form-control" id="unit" name="unit" required>
          </div>
          <div class="mb-3">
            <label for="reorderLevel" class="form-label">Reorder Level</label>
            <input type="number" class="form-control" id="reorderLevel" name="reorderLevel" required>
          </div>
          <div class="mb-3">
            <label for="supplierID" class="form-label">Supplier</label>
            <select class="form-control" id="supplierID" name="supplierID" required>
              <?php foreach ($suppliers as $supplier): ?>
                <option value="<?php echo $supplier['SupplierID']; ?>"><?php echo $supplier['SupplierName']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Add Item</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>