<?php
require_once '../../connection/db.php';

// Fetch all orders with customer names
$stmt = $conn->query("
    SELECT `Order`.*, User.Username 
    FROM `Order` 
    INNER JOIN User ON `Order`.CustomerID = User.UserID
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Orders - ELCHEF</title>
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
        <h2>Manage Orders</h2>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer</th>
              <th>Order Date</th>
              <th>Status</th>
              <th>Total Amount</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?php echo $order['OrderID']; ?></td>
                <td><?php echo $order['Username']; ?></td>
                <td><?php echo $order['OrderDate']; ?></td>
                <td><?php echo $order['OrderStatus']; ?></td>
                <td>$<?php echo number_format($order['TotalAmount'], 2); ?></td>
                <td>
                  <a href="view_order.php?id=<?php echo $order['OrderID']; ?>" class="btn btn-info btn-sm">View</a>
                  <a href="edit_order.php?id=<?php echo $order['OrderID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="delete_order.php?id=<?php echo $order['OrderID']; ?>" class="btn btn-danger btn-sm">Delete</a>
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