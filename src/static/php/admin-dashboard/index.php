<?php
session_start();
require_once '../../connection/db.php';

// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details from the database
$userID = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT UserID, Username, FirstName, LastName, ProfilePictureURL, 
Role, DATE_FORMAT(LastLogin, '%M %d, %Y %h:%i %p')
as LastLoginFormatted FROM User WHERE UserID = :userID");
$stmt->execute([':userID' => $userID]);
$user = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: login.php');
    exit();
}

// Update LastLogin timestamp
$stmt = $conn->prepare("UPDATE User SET LastLogin = CURRENT_TIMESTAMP WHERE UserID = :userID");
$stmt->execute([':userID' => $userID]);

// Fetch statistics
$stmt = $conn->query("SELECT COUNT(*) as total_users FROM User");
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

$stmt = $conn->query("SELECT COUNT(*) as total_orders FROM `Order`");
$total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total_orders'];

$stmt = $conn->query("SELECT COUNT(*) as total_reservations FROM Reservation");
$total_reservations = $stmt->fetch(PDO::FETCH_ASSOC)['total_reservations'];

$stmt = $conn->query("SELECT COUNT(*) as total_menu_items FROM MenuItem");
$total_menu_items = $stmt->fetch(PDO::FETCH_ASSOC)['total_menu_items'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ELCHEF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">

</head>

<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>ELCHEF Admin</h3>
            </div>
            <ul class="list-unstyled components">
                <li class="active">
                    <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li>
                    <a href="users.php"><i class="fas fa-users"></i> Users</a>
                </li>
                <li>
                    <a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a>
                </li>
                <li>
                    <a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a>
                </li>
                <li>
                    <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                </li>
                <li>
                    <a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a>
                </li>
                <li>
                    <a href="inventory.php"><i class="fas fa-box"></i> Inventory</a>
                </li>
            </ul>
        </nav>

        <div id="content">
            <div class="header">
                <button type="button" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo">ELCHEF</div>
                <div class="admin-dropdown">
                    <a href="#" class="admin-profile dropdown-toggle" data-bs-toggle="dropdown">
                        <?php if (!empty($user['ProfilePictureURL'])): ?>
                            <img src="../../<?php echo htmlspecialchars($user['ProfilePictureURL']); ?>"
                                alt="Profile Picture">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-2x"></i>
                        <?php endif; ?>
                        <div class="admin-info d-none d-md-flex">
                            <span class="admin-name">
                                <?php echo htmlspecialchars(trim($user['FirstName'] . ' ' . $user['LastName'])) ?: htmlspecialchars($user['Username']); ?>
                            </span>
                            <span class="admin-role">
                                <?php echo ucfirst(htmlspecialchars($user['Role'])); ?>
                            </span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Welcome, <?php echo htmlspecialchars($user['FirstName'] ?: $user['Username']); ?>!</h2>
                    <?php if ($user['LastLoginFormatted']): ?>
                        <small class="text-muted">
                            Last login: <?php echo htmlspecialchars($user['LastLoginFormatted']); ?>
                        </small>
                    <?php endif; ?>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3 stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-users stat-icon"></i>
                                <h5 class="card-title">Total Users</h5>
                                <p class="stat-value"><?php echo $total_users; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3 stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart stat-icon"></i>
                                <h5 class="card-title">Total Orders</h5>
                                <p class="stat-value"><?php echo $total_orders; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3 stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt stat-icon"></i>
                                <h5 class="card-title">Total Reservations</h5>
                                <p class="stat-value"><?php echo $total_reservations; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger mb-3 stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-utensils stat-icon"></i>
                                <h5 class="card-title">Total Menu Items</h5>
                                <p class="stat-value"><?php echo $total_menu_items; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
</body>

</html>