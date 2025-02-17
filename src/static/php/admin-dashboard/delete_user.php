<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: users.php?error=' . urlencode('No user ID provided'));
    exit;
}

try {
    $userId = (int)$_GET['id'];
    $action = $_GET['action'] ?? 'deactivate';
    
    // First check if the user exists
    $checkStmt = $conn->prepare("SELECT UserID, Username, Role, COALESCE(Status, 'active') as Status FROM User WHERE UserID = ?");
    $checkStmt->execute([$userId]);
    $user = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        header('Location: users.php?error=' . urlencode('User not found'));
        exit;
    }
    
    if ($action === 'deactivate') {
        // Don't allow deactivation if it's the last active admin
        if ($user['Role'] === 'admin') {
            $adminCheckStmt = $conn->prepare("SELECT COUNT(*) as admin_count FROM User WHERE Role = 'admin' AND (Status = 'active' OR Status IS NULL) AND UserID != ?");
            $adminCheckStmt->execute([$userId]);
            $adminCount = (int)$adminCheckStmt->fetch(PDO::FETCH_ASSOC)['admin_count'];
            
            if ($adminCount === 0) {
                header('Location: users.php?error=' . urlencode('Cannot deactivate the last active admin user'));
                exit;
            }
        }
        
        // Soft delete by setting status to inactive
        $updateStmt = $conn->prepare("UPDATE User SET Status = 'inactive' WHERE UserID = ?");
        if (!$updateStmt->execute([$userId])) {
            throw new PDOException("Failed to execute update statement");
        }
        
        if ($updateStmt->rowCount() > 0) {
            header('Location: users.php?message=' . urlencode('User "' . $user['Username'] . '" has been deactivated'));
        } else {
            header('Location: users.php?error=' . urlencode('Failed to deactivate user - no rows updated'));
        }
    } else if ($action === 'reactivate') {
        // Reactivate user
        $updateStmt = $conn->prepare("UPDATE User SET Status = 'active' WHERE UserID = ?");
        if (!$updateStmt->execute([$userId])) {
            throw new PDOException("Failed to execute update statement");
        }
        
        if ($updateStmt->rowCount() > 0) {
            header('Location: users.php?message=' . urlencode('User "' . $user['Username'] . '" has been reactivated'));
        } else {
            header('Location: users.php?error=' . urlencode('Failed to reactivate user - no rows updated'));
        }
    } else {
        header('Location: users.php?error=' . urlencode('Invalid action specified'));
    }
} catch (PDOException $e) {
    // Log the error for debugging
    error_log("Database error in delete_user.php: " . $e->getMessage());
    header('Location: users.php?error=' . urlencode('Database error: ' . $e->getMessage()));
}
exit;
?> 