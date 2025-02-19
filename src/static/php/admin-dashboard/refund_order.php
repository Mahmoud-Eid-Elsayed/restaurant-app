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

if (!isset($_GET['reason']) || empty(trim($_GET['reason']))) {
    header('Location: orders.php?error=' . urlencode('Refund reason is required'));
    exit;
}

$orderId = (int) $_GET['id'];
$reason = trim($_GET['reason']);

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if the order exists and get its details
    $stmt = $conn->prepare("
        SELECT o.*, u.Username, u.Email 
        FROM `Order` o
        INNER JOIN User u ON o.CustomerID = u.UserID
        WHERE o.OrderID = ?
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if (!$order) {
        throw new Exception('Order not found');
    }

    // Check if the order can be refunded
    if ($order['OrderStatus'] !== 'Completed') {
        throw new Exception('Only completed orders can be refunded');
    }

    // Update the order status to Refunded
    $stmt = $conn->prepare("
        UPDATE `Order` 
        SET OrderStatus = 'Refunded',
            LastModified = CURRENT_TIMESTAMP,
            RefundReason = ?,
            RefundDate = CURRENT_TIMESTAMP
        WHERE OrderID = ?
    ");
    $stmt->execute([$reason, $orderId]);

    // Check if the update was successful
    if ($stmt->rowCount() === 0) {
        throw new Exception('Failed to refund order');
    }

    // Log the refund in order history
    $stmt = $conn->prepare("
        INSERT INTO OrderStatusHistory (
            OrderID,
            StatusFrom,
            StatusTo,
            ChangedAt,
            Notes
        ) VALUES (?, 'Completed', 'Refunded', CURRENT_TIMESTAMP, ?)
    ");
    $stmt->execute([$orderId, "Order refunded. Reason: " . $reason]);

    // Create a refund record
    $stmt = $conn->prepare("
        INSERT INTO Refund (
            OrderID,
            RefundAmount,
            RefundDate,
            RefundReason,
            Status
        ) VALUES (?, ?, CURRENT_TIMESTAMP, ?, 'Processed')
    ");
    $stmt->execute([$orderId, $order['TotalAmount'], $reason]);

    // Commit the transaction
    $conn->commit();

    // Send email notification to customer (if email is available)
    if ($order['Email']) {
        $to = $order['Email'];
        $subject = "Order #$orderId Refund Processed";
        $emailMessage = "Dear " . htmlspecialchars($order['Username']) . ",\n\n";
        $emailMessage .= "Your order #$orderId has been refunded.\n";
        $emailMessage .= "Refund Amount: $" . number_format($order['TotalAmount'], 2) . "\n";
        $emailMessage .= "Reason: " . $reason . "\n\n";
        $emailMessage .= "If you have any questions, please contact our support team.\n\n";
        $emailMessage .= "Best regards,\nELCHEF Restaurant";

        $headers = "From: noreply@elchef.com\r\n";
        $headers .= "Reply-To: support@elchef.com\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        // Log email sending attempt
        error_log("Sending refund notification email to: " . $order['Email']);

        // Send email (in production, use a proper email service)
        @mail($to, $subject, $emailMessage, $headers);
    }

    $message = 'Order #' . $orderId . ' has been refunded successfully';

} catch (Exception $e) {
    // Rollback the transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Log the error for debugging
    error_log("Error refunding order (ID: $orderId): " . $e->getMessage());

    $error = $e->getMessage();
} catch (PDOException $e) {
    // Rollback the transaction on database error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Log the database error for debugging
    error_log("Database error while refunding order (ID: $orderId): " . $e->getMessage());

    // Provide a user-friendly error message
    $error = 'A database error occurred while trying to process the refund';
}

// Redirect back to the orders page with appropriate message
if ($error) {
    header('Location: orders.php?error=' . urlencode($error));
} else {
    header('Location: orders.php?message=' . urlencode($message));
}
exit;
?>