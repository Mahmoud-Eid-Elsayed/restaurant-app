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
$stmt = $conn->prepare("
    SELECT UserID, Username, FirstName, LastName, ProfilePictureURL, Role, Status,
           DATE_FORMAT(LastLogin, '%M %d, %Y %h:%i %p') as LastLoginFormatted
    FROM User 
    WHERE UserID = :userID
");
$stmt->execute([':userID' => $userID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

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
    <style>
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            text-decoration: none;
            color: inherit;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .admin-profile:hover {
            background: rgba(0,0,0,0.05);
        }
        .admin-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .admin-info {
            display: flex;
            flex-direction: column;
        }
        .admin-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: #2c3e50;
        }
        .admin-role {
            font-size: 0.8rem;
            color: #6c757d;
        }
        .welcome-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .welcome-section h2 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: white;
        }
        .welcome-section p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
        }
        .stat-card {
            border-radius: 1rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }
        .quick-actions {
            margin-top: 2rem;
        }
        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            border-radius: 0.5rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .quick-action-btn:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }
        .quick-action-btn i {
            font-size: 1.2rem;
        }
        @media (max-width: 768px) {
            .stat-card {
                margin-bottom: 1rem;
            }
            .welcome-section {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            .stat-value {
                font-size: 2rem;
            }
        }
    </style>
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
                            <img src="../../<?php echo htmlspecialchars($user['ProfilePictureURL']); ?>" alt="Profile Picture">
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
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="main-content">
                <div class="welcome-section">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h2>Welcome back, <?php echo htmlspecialchars($user['FirstName'] ?: $user['Username']); ?>!</h2>
                            <?php if ($user['LastLoginFormatted']): ?>
                                <p><i class="fas fa-clock me-2"></i>Last login: <?php echo htmlspecialchars($user['LastLoginFormatted']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="card text-white bg-primary stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-users stat-icon"></i>
                                <h3 class="stat-value"><?php echo $total_users; ?></h3>
                                <p class="stat-label">Total Users</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card text-white bg-success stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-cart stat-icon"></i>
                                <h3 class="stat-value"><?php echo $total_orders; ?></h3>
                                <p class="stat-label">Total Orders</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card text-white bg-warning stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt stat-icon"></i>
                                <h3 class="stat-value"><?php echo $total_reservations; ?></h3>
                                <p class="stat-label">Total Reservations</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="card text-white bg-danger stat-card">
                            <div class="card-body text-center">
                                <i class="fas fa-utensils stat-icon"></i>
                                <h3 class="stat-value"><?php echo $total_menu_items; ?></h3>
                                <p class="stat-label">Menu Items</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="quick-actions">
                    <h4 class="mb-3">Quick Actions</h4>
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="orders.php" class="quick-action-btn">
                                <i class="fas fa-clipboard-list"></i>
                                View Recent Orders
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="reservations.php" class="quick-action-btn">
                                <i class="fas fa-calendar-plus"></i>
                                Manage Reservations
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="menu_items.php" class="quick-action-btn">
                                <i class="fas fa-plus-circle"></i>
                                Add Menu Item
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="users.php" class="quick-action-btn">
                                <i class="fas fa-user-plus"></i>
                                Add New User
                            </a>
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