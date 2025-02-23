<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: orders.php");
  exit();
}

// Fetch the order details
$stmt = $conn->prepare("SELECT * FROM `Order` WHERE OrderID = :id");
$stmt->execute([':id' => $orderId]);
$order = $stmt->fetch();

if ($order) {
  // Add notification for the customer
  $customerMessage = "Your order #$orderId has been deleted.";
  $stmt = $conn->prepare("
        INSERT INTO Notification (UserID, OrderID, NotificationType, Message)
        VALUES (:userID, :orderID, 'OrderDeleted', :message)
    ");
  $stmt->execute([
    ':userID' => $order['CustomerID'],
    ':orderID' => $orderId,
    ':message' => $customerMessage
  ]);

  // Add notification for the admin
  $adminMessage = "Order #$orderId has been deleted.";
  $stmt = $conn->prepare("
        INSERT INTO Notification (UserID, OrderID, NotificationType, Message)
        VALUES (NULL, :orderID, 'OrderDeleted', :message)
    ");
  $stmt->execute([
    ':orderID' => $orderId,
    ':message' => $adminMessage
  ]);
}

// Delete the order
$stmt = $conn->prepare("DELETE FROM `Order` WHERE OrderID = :id");
$stmt->execute([':id' => $orderId]);

header("Location: orders.php");
exit();
?>