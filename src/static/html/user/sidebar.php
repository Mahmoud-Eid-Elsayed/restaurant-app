<?php
require_once __DIR__ . '/../../connection/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Customer or admin ID
$notificationID = $_GET['id'] ?? null;

try {
    if ($notificationID) {
        // Mark the notification as read
        $query = "UPDATE Notification SET IsRead = TRUE WHERE NotificationID = ? AND UserID = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$notificationID, $user_id]);

        // Fetch the notification details
        $query = "SELECT * FROM Notification WHERE NotificationID = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$notificationID]);
        $notification = $stmt->fetch();

        if (!$notification) {
            die("Notification not found.");
        }
    }

    // Fetch unread notifications count
    $query = "SELECT COUNT(*) AS unreadCount FROM Notification WHERE UserID = ? AND IsRead = FALSE";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);
    $unreadCount = $stmt->fetchColumn();

} catch (PDOException $e) {
    die("Error fetching notification: " . $e->getMessage());
}

// Fetch user data
$user = new User($conn);
$userData = $user->getUserById($user_id);

if (!$userData) {
    die("User not found.");
}
?>

<div class="d-flex">
    <div class="sidebar text-white p-3" style="width: 250px; height: 100vh; background-color: #87CEEB;">
        <div class="text-center mb-4">
            <img src="<?= $userData['ProfilePictureURL'] ?? 'default-avatar.png' ?>" alt="Profile Picture"
                class="rounded-circle border border-white" width="100">
            <h5 class="mt-2 text-dark"><?= $_SESSION['username'] ?? 'User' ?></h5>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="userData.php" class="nav-link text-dark">📄 User Profile</a>
            </li>
            <li class="nav-item">
                <a href="editProfile.php" class="nav-link text-dark">✏ Edit Profile</a>
            </li>
            <li class="nav-item">
                <a href="customer_orders-history.php" class="nav-link text-dark">🛒 My Orders</a>
            </li>
            <li class="nav-item">
                <a href="../../php/menu/menu.php" class="nav-link text-dark">🍽 Menu</a>
            </li>
            <li class="nav-item">
                <a href="customer_notifications.php" class="nav-link text-dark">
                    🔔 Notifications 
                </a>
            </li>
            <li class="nav-item">
                <a href="../../php/menu/cart.php" class="nav-link text-dark">🛍 Cart</a>
            </li>
            <li class="nav-item mt-3">
                <a href="logout.php" class="btn btn-danger w-100">🚪Logout</a>
            </li>
        </ul>
    </div>
</div>
