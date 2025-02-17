<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: ../orders.php");
  exit();
}

$orderId = $_GET['id'];


$stmt = $conn->prepare("
    SELECT `Order`.*, User.Username 
    FROM `Order` 
    INNER JOIN User ON `Order`.CustomerID = User.UserID
    WHERE OrderID = :id
");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$order) {
  header("Location: ../orders.php");
  exit();
}


$stmt = $conn->prepare("
    SELECT OrderItem.*, MenuItem.ItemName 
    FROM OrderItem 
    INNER JOIN MenuItem ON OrderItem.ItemID = MenuItem.ItemID
    WHERE OrderID = :id
");
$stmt->execute([':id' => $orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Order - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
</head>

<body>
  <div class="wrapper">

    <div id="content">
      <div class="header">
        <!-- Header content -->
      </div>
      <div class="main-content">
        <h2>View Order</h2>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Order Details</h5>
            <p><strong>Order ID:</strong> <?php echo $order['OrderID']; ?></p>
            <p><strong>Customer:</strong> <?php echo $order['Username']; ?></p>
            <p><strong>Order Date:</strong> <?php echo $order['OrderDate']; ?></p>
            <p><strong>Status:</strong> <?php echo $order['OrderStatus']; ?></p>
            <p><strong>Total Amount:</strong> $<?php echo number_format($order['TotalAmount'], 2); ?></p>
          </div>
        </div>
        <h4>Order Items</h4>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orderItems as $item): ?>
              <tr>
                <td><?php echo $item['ItemName']; ?></td>
                <td><?php echo $item['Quantity']; ?></td>
                <td>$<?php echo number_format($item['PriceAtTimeOfOrder'], 2); ?></td>
                <td>$<?php echo number_format($item['Quantity'] * $item['PriceAtTimeOfOrder'], 2); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <a href="../orders.php" class="btn btn-secondary">Back to Orders</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>