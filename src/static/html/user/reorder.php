<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

if (!isset($_GET['order_id'])) {
  die("Order ID not provided.");
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

try {
  // Fetch the previous order details
  $query = "SELECT * FROM `order` WHERE OrderID = ? AND CustomerID = ?";
  $stmt = $conn->prepare($query);
  $stmt->execute([$order_id, $user_id]);
  $order = $stmt->fetch();

  if (!$order) {
    die("Order not found.");
  }

  // Fetch the order items
  $query = "SELECT oi.*, mi.Price FROM OrderItem oi
              INNER JOIN MenuItem mi ON oi.ItemID = mi.ItemID
              WHERE oi.OrderID = ?";
  $stmt = $conn->prepare($query);
  $stmt->execute([$order_id]);
  $order_items = $stmt->fetchAll();

  // Create a new order
  $query = "INSERT INTO `order` (CustomerID, OrderDate, TotalAmount, Notes, OrderStatus)
              VALUES (?, NOW(), ?, ?, 'Pending')";
  $stmt = $conn->prepare($query);
  $stmt->execute([$user_id, $order['TotalAmount'], $order['Notes']]);
  $new_order_id = $conn->lastInsertId();

  // Add items to the new order
  foreach ($order_items as $item) {
    $query = "INSERT INTO OrderItem (OrderID, ItemID, Quantity, Price)
                  VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$new_order_id, $item['ItemID'], $item['Quantity'], $item['Price']]);
  }

  // Send notifications
  $message = "Customer #$user_id has reordered Order #$order_id as Order #$new_order_id.";
  $query = "INSERT INTO Notification (UserID, Message, NotificationType, IsRead)
              VALUES (NULL, ?, 'Reorder', 0)";
  $stmt = $conn->prepare($query);
  $stmt->execute([$message]);

  // Redirect with success message
  header("Location: customer_orders-history.php?message=Order reordered successfully.");
  exit();
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>