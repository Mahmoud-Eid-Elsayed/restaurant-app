<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: orders.php");
  exit();
}

$orderId = $_GET['id'];

// Fetch order details
$stmt = $conn->prepare("
    SELECT `Order`.*, User.Username 
    FROM `Order` 
    INNER JOIN User ON `Order`.CustomerID = User.UserID
    WHERE OrderID = :id
");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$order) {
  header("Location: orders.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $status = $_POST['status'];

  $stmt = $conn->prepare("
        UPDATE `Order` 
        SET OrderStatus = :status
        WHERE OrderID = :id
    ");
  $stmt->execute([
    ':status' => $status,
    ':id' => $orderId
  ]);

  header("Location: orders.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Order - ELCHEF</title>
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
        <h2>Edit Order</h2>
        <form method="POST">
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
              <option value="Pending" <?php echo $order['OrderStatus'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="Preparing" <?php echo $order['OrderStatus'] == 'Preparing' ? 'selected' : ''; ?>>Preparing
              </option>
              <option value="Ready" <?php echo $order['OrderStatus'] == 'Ready' ? 'selected' : ''; ?>>Ready</option>
              <option value="Delivered" <?php echo $order['OrderStatus'] == 'Delivered' ? 'selected' : ''; ?>>Delivered
              </option>
              <option value="Cancelled" <?php echo $order['OrderStatus'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled
              </option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Update Order</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>