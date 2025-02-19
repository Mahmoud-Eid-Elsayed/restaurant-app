<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: menu_categories.php?error=' . urlencode('Invalid category ID'));
    exit;
}

$categoryId = (int) $_GET['id'];

try {
    // Begin transaction
    $conn->beginTransaction();

    // First check if the category exists and get its name
    $checkStmt = $conn->prepare("SELECT CategoryName FROM MenuCategory WHERE CategoryID = ?");
    $checkStmt->execute([$categoryId]);
    $category = $checkStmt->fetch();

    if (!$category) {
        throw new Exception('Category not found');
    }

    // Check if there are any menu items in this category
    $itemCheckStmt = $conn->prepare("SELECT COUNT(*) as count FROM MenuItem WHERE CategoryID = ?");
    $itemCheckStmt->execute([$categoryId]);
    $itemCount = (int) $itemCheckStmt->fetch()['count'];

    if ($itemCount > 0) {
        throw new Exception(
            'Cannot delete category "' . $category['CategoryName'] . '" because it contains ' . $itemCount . ' menu items. ' .
            'Please reassign or delete the menu items first.'
        );
    }

    // Delete the category
    $deleteStmt = $conn->prepare("DELETE FROM MenuCategory WHERE CategoryID = ?");
    $deleteStmt->execute([$categoryId]);

    if ($deleteStmt->rowCount() === 0) {
        throw new Exception('Failed to delete category');
    }

    // Commit the transaction
    $conn->commit();

    // Redirect with success message
    header('Location: menu_categories.php?message=' . urlencode(
        'Category "' . $category['CategoryName'] . '" has been deleted successfully'
    ));

} catch (Exception $e) {
    // Rollback the transaction
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    // Log the error
    error_log("Error in delete_menu_category.php: " . $e->getMessage());

    // Redirect with error message
    header('Location: menu_categories.php?error=' . urlencode($e->getMessage()));
}
exit;
?>