<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize response variables
$error = null;
$message = null;

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: orders.php?error=' . urlencode('Invalid order ID'));
    exit;
}

$orderId = (int)$_GET['id'];

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if the order exists and get its details
    $stmt = $conn->prepare("
        SELECT o.*, u.Username 
        FROM `Order` o
        INNER JOIN User u ON o.CustomerID = u.UserID
        WHERE o.OrderID = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception('Order not found');
    }

    // Check if the order can be cancelled
    if ($order['OrderStatus'] === 'Completed') {
        throw new Exception('Cannot cancel a completed order');
    }

    if ($order['OrderStatus'] === 'Cancelled') {
        throw new Exception('Order is already cancelled');
    }

    // Update the order status to Cancelled
    $stmt = $conn->prepare("
        UPDATE `Order` 
        SET OrderStatus = 'Cancelled',
            LastModified = CURRENT_TIMESTAMP
        WHERE OrderID = ?
    ");
    $stmt->execute([$orderId]);

    // Check if the update was successful
    if ($stmt->rowCount() === 0) {
        throw new Exception('Failed to cancel order');
    }

    // Log the cancellation
    $stmt = $conn->prepare("
        INSERT INTO OrderStatusHistory (
            OrderID,
            StatusFrom,
            StatusTo,
            ChangedAt,
            Notes
        ) VALUES (?, ?, 'Cancelled', CURRENT_TIMESTAMP, 'Order cancelled by admin')
    ");
    $stmt->execute([$orderId, $order['OrderStatus']]);

    // Commit the transaction
    $conn->commit();
    $message = 'Order #' . $orderId . ' has been cancelled successfully';

} catch (Exception $e) {
    // Rollback the transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log the error for debugging
    error_log("Error cancelling order (ID: $orderId): " . $e->getMessage());
    
    $error = $e->getMessage();
} catch (PDOException $e) {
    // Rollback the transaction on database error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log the database error for debugging
    error_log("Database error while cancelling order (ID: $orderId): " . $e->getMessage());
    
    // Provide a user-friendly error message
    $error = 'A database error occurred while trying to cancel the order';
}

// Redirect back to the orders page with appropriate message
if ($error) {
    header('Location: orders.php?error=' . urlencode($error));
} else {
    header('Location: orders.php?message=' . urlencode($message));
}
exit;
?> 