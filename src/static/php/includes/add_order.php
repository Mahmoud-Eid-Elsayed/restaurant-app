<?php
// Include the database connection
require_once __DIR__ . '../../connection/db.php';

// Assume these values are available after an order is placed
$customer_id = $_SESSION['user_id']; // Customer ID from session
$order_id = $new_order_id; // The newly created order ID

// Insert notification for the customer
$customer_message = "Your order #{$order_id} has been placed successfully!";
$query = "INSERT INTO Notification (UserID, OrderID, NotificationType, Message) VALUES (?, ?, 'order', ?)";
$stmt = $conn->prepare($query);
$stmt->execute([$customer_id, $order_id, $customer_message]);

// Insert notification for the admin
$admin_message = "A new order #{$order_id} has been placed by Customer #{$customer_id}.";
$query = "INSERT INTO Notification (UserID, OrderID, NotificationType, Message) VALUES (NULL, ?, 'order', ?)";
$stmt = $conn->prepare($query);
$stmt->execute([$order_id, $admin_message]);

echo "Notifications inserted successfully!";
?>