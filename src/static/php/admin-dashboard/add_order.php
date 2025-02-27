<?php
require_once '../../connection/db.php';

// Fetch all customers
$stmt = $conn->query("SELECT * FROM User WHERE Role = 'Customer'");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all menu items
$stmt = $conn->query("SELECT * FROM MenuItem");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $customerID = $_POST['customerID'];
  $orderStatus = $_POST['orderStatus'];
  $totalAmount = $_POST['totalAmount'];
  $deliveryAddress = $_POST['deliveryAddress'];
  $notes = $_POST['notes'];
  $items = $_POST['items'];

  // Insert order
  $stmt = $conn->prepare("
        INSERT INTO `order` (CustomerID, OrderStatus, TotalAmount, DeliveryAddress, Notes)
        VALUES (:customerID, :orderStatus, :totalAmount, :deliveryAddress, :notes)
    ");
  $stmt->execute([
    ':customerID' => $customerID,
    ':orderStatus' => $orderStatus,
    ':totalAmount' => $totalAmount,
    ':deliveryAddress' => $deliveryAddress,
    ':notes' => $notes
  ]);

  $orderID = $conn->lastInsertId();

  // Insert order items
  foreach ($items as $item) {
    $stmt = $conn->prepare("
            INSERT INTO orderitem (OrderID, ItemID, Quantity, PriceAtTimeOfOrder)
            VALUES (:orderID, :itemID, :quantity, :price)
        ");
    $stmt->execute([
      ':orderID' => $orderID,
      ':itemID' => $item['itemID'],
      ':quantity' => $item['quantity'],
      ':price' => $item['price']
    ]);
  }

  header("Location: orders.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Order - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
</head>

<body>
  <div class="wrapper">

    <div id="content">
      <div class="header">

      </div>
      <div class="main-content">
        <h2>Add Order</h2>
        <form method="POST">
          <div class="mb-3">
            <label for="customerID" class="form-label">Customer</label>
            <select class="form-control" id="customerID" name="customerID" required>
              <?php foreach ($customers as $customer): ?>
                <option value="<?php echo $customer['UserID']; ?>"><?php echo $customer['Username']; ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="orderStatus" class="form-label">Status</label>
            <select class="form-control" id="orderStatus" name="orderStatus" required>
              <option value="Pending">Pending</option>
              <option value="Preparing">Preparing</option>
              <option value="Ready">Ready</option>
              <option value="Delivered">Delivered</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="totalAmount" class="form-label">Total Amount</label>
            <input type="number" step="0.01" class="form-control" id="totalAmount" name="totalAmount" required>
          </div>
          <div class="mb-3">
            <label for="deliveryAddress" class="form-label">Delivery Address</label>
            <textarea class="form-control" id="deliveryAddress" name="deliveryAddress" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
          </div>
          <h4>Order Items</h4>
          <div id="orderItems">
            <div class="order-item mb-3">
              <div class="row">
                <div class="col-md-5">
                  <label for="itemID" class="form-label">Item</label>
                  <select class="form-control" name="items[0][itemID]" required>
                    <?php foreach ($menuItems as $item): ?>
                      <option value="<?php echo $item['ItemID']; ?>"><?php echo $item['ItemName']; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="quantity" class="form-label">Quantity</label>
                  <input type="number" class="form-control" name="items[0][quantity]" required>
                </div>
                <div class="col-md-3">
                  <label for="price" class="form-label">Price</label>
                  <input type="number" step="0.01" class="form-control" name="items[0][price]" required>
                </div>
                <div class="col-md-1">
                  <button type="button" class="btn btn-danger mt-4" onclick="removeItem(this)">Remove</button>
                </div>
              </div>
            </div>
          </div>
          <button type="button" class="btn btn-secondary mb-3" onclick="addItem()">Add Item</button>
          <button type="submit" class="btn btn-primary">Add Order</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    let itemCount = 1;

    function addItem() {
      const orderItems = document.getElementById('orderItems');
      const newItem = document.createElement('div');
      newItem.classList.add('order-item', 'mb-3');
      newItem.innerHTML = `
                <div class="row">
                    <div class="col-md-5">
                        <label for="itemID" class="form-label">Item</label>
                        <select class="form-control" name="items[${itemCount}][itemID]" required>
                            <?php foreach ($menuItems as $item): ?>
                                <option value="<?php echo $item['ItemID']; ?>"><?php echo $item['ItemName']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" name="items[${itemCount}][quantity]" required>
                    </div>
                    <div class="col-md-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" name="items[${itemCount}][price]" required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger mt-4" onclick="removeItem(this)">Remove</button>
                    </div>
                </div>
            `;
      orderItems.appendChild(newItem);
      itemCount++;
    }

    function removeItem(button) {
      button.closest('.order-item').remove();
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>