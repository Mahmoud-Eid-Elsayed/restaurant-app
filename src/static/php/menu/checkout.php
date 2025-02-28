<?php
session_start();

if (empty($_SESSION['cart'])) {
  header("Location: cart.php");
  exit();
}

require_once __DIR__ . '/../../connection/db.php';

try {
  $conn->beginTransaction();

  $query = "INSERT INTO `order` (CustomerID, OrderDate, TotalAmount, OrderStatus) VALUES (?, NOW(), ?, 'Pending')";
  $stmt = $conn->prepare($query);
  $stmt->execute([$_SESSION['user_id'], array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $_SESSION['cart']))]);
  $orderID = $conn->lastInsertId();

  $query = "INSERT INTO orderitem (OrderID, ItemID, Quantity, PriceAtTimeOfOrder) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($query);
  foreach ($_SESSION['cart'] as $id => $item) {
    $stmt->execute([$orderID, $id, $item['quantity'], $item['price']]); // Add the price
  }


  $query = "INSERT INTO notification (UserID, Message, NotificationType, Timestamp) VALUES (?, ?, ?, NOW())";
  $stmt = $conn->prepare($query);

  $stmt->execute([$_SESSION['user_id'], "Your order #$orderID has been placed successfully.", 'customer']);

  $stmt->execute([null, "New order #$orderID has been placed by customer ID {$_SESSION['user_id']}.", 'admin']);

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