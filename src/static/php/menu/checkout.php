<?php
session_start();

if (empty($_SESSION['cart'])) {
  header("Location: cart.php");
  exit();
}

require_once __DIR__ . '/../../connection/db.php';

try {
  $conn->beginTransaction();

  $query = "INSERT INTO `Order` (CustomerID, OrderDate, TotalAmount, OrderStatus) VALUES (?, NOW(), ?, 'Pending')";
  $stmt = $conn->prepare($query);
  $stmt->execute([$_SESSION['user_id'], array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $_SESSION['cart']))]);
  $orderID = $conn->lastInsertId();

  $query = "INSERT INTO OrderItem (OrderID, ItemID, Quantity) VALUES (?, ?, ?)";
  $stmt = $conn->prepare($query);
  foreach ($_SESSION['cart'] as $id => $item) {
    $stmt->execute([$orderID, $id, $item['quantity']]);
  }

  $query = "INSERT INTO Notification (UserID, Message, Timestamp) VALUES (?, ?, NOW())";
  $stmt = $conn->prepare($query);

  $stmt->execute([$_SESSION['user_id'], "Your order #$orderID has been placed successfully."]);

  $stmt->execute([null, "New order #$orderID has been placed by customer ID {$_SESSION['user_id']}."]);

  $conn->commit();

  // Clear the cart after checkout
  unset($_SESSION['cart']);

  // Redirect to the success page
  header("Location: checkout_success.php");
  exit();
} catch (PDOException $e) {
  $conn->rollBack();
  die("Error during checkout: " . $e->getMessage());
}
?>