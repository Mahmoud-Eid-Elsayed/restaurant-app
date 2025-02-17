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
    header('Location: menu_items.php?error=' . urlencode('Invalid menu item ID'));
    exit;
}

$itemId = (int)$_GET['id'];

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if the item exists and get its details
    $stmt = $conn->prepare("SELECT ItemName FROM MenuItem WHERE ItemID = ?");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$item) {
        throw new Exception('Menu item not found');
    }

    // Check if the item has any associated orders
    $stmt = $conn->prepare("
        SELECT COUNT(*) as order_count 
        FROM OrderItem 
        WHERE ItemID = ?
    ");
    $stmt->execute([$itemId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['order_count'] > 0) {
        throw new Exception('Cannot delete menu item: It has associated orders');
    }

    // Delete the menu item
    $stmt = $conn->prepare("DELETE FROM MenuItem WHERE ItemID = ?");
    $stmt->execute([$itemId]);

    // Check if the deletion was successful
    if ($stmt->rowCount() === 0) {
        throw new Exception('Failed to delete menu item');
    }

    // Commit the transaction
    $conn->commit();
    $message = 'Menu item "' . htmlspecialchars($item['ItemName']) . '" has been deleted successfully';

} catch (Exception $e) {
    // Rollback the transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log the error for debugging
    error_log("Error deleting menu item (ID: $itemId): " . $e->getMessage());
    
    $error = $e->getMessage();
} catch (PDOException $e) {
    // Rollback the transaction on database error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log the database error for debugging
    error_log("Database error while deleting menu item (ID: $itemId): " . $e->getMessage());
    
    // Provide a user-friendly error message
    $error = 'A database error occurred while trying to delete the menu item';
}

// Redirect back to the menu items page with appropriate message
if ($error) {
    header('Location: menu_items.php?error=' . urlencode($error));
} else {
    header('Location: menu_items.php?message=' . urlencode($message));
}
exit;
?>