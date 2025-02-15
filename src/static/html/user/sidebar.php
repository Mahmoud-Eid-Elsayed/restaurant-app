<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require '../../connection/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user = new User($conn);
$userData = $user->getUserById($_SESSION['user_id']);

if (!$userData) {
    die("User not found.");
}
// $user_id = $_SESSION['user_id'];
// $stmt = $conn->prepare("SELECT * FROM User WHERE UserID = ?");
// $stmt->execute([$user_id]);
// $user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<div class="d-flex">

    <div class="sidebar text-white p-3" style="width: 250px; height: 100vh; background-color: #87CEEB;"> 
        <div class="text-center mb-4">

            <img src="<?= $userData['ProfilePictureURL']  ?? 'default-avatar.png' ?>" 
                 alt="Profile Picture" 
                 class="rounded-circle border border-white" 
                 width="100" >

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
                <a href="orders.php" class="nav-link text-dark">🛒 My Orders</a>
            </li>
            <li class="nav-item">
                <a href="menu.php" class="nav-link text-dark">🍽 Menu</a>
            </li>
            <li class="nav-item">
                <a href="notifications.php" class="nav-link text-dark">🔔 Notifications</a>
            </li>
            <li class="nav-item">
                <a href="cart.php" class="nav-link text-dark">🛍 Cart</a>
            </li>
            <li class="nav-item mt-3">
                <a href="logout.php" class="btn btn-danger w-100">🚪 Logout</a>
            </li>
        </ul>
    </div>
</div>
