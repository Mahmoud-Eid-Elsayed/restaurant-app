<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Staff') {
    header("Location: login.php");
    exit();
}
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details from the database
$userID = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT UserID, Username, FirstName, LastName, ProfilePictureURL, 
Role, DATE_FORMAT(LastLogin, '%M %d, %Y %h:%i %p') as LastLoginFormatted FROM User WHERE UserID = :userID");
$stmt->execute([':userID' => $userID]);
$user = $stmt->fetch(); // Use fetch() instead of fetchAll() since we expect a single row

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

// Fetch daily, weekly, and monthly reports //
$stmt = $conn->query("
    SELECT 
        COALESCE(COUNT(*), 0) as order_count,
        COALESCE(SUM(TotalAmount), 0) as total_revenue,
        DATE_FORMAT(CURRENT_DATE, '%M %d, %Y') as report_date
    FROM `Order`
    WHERE DATE(OrderDate) = CURRENT_DATE
    AND OrderStatus = 'Completed'
");
$daily_report = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->query("
    SELECT 
        COALESCE(COUNT(*), 0) as order_count,
        COALESCE(SUM(TotalAmount), 0) as total_revenue,
        DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY), '%M %d') as start_date,
        DATE_FORMAT(CURRENT_DATE, '%M %d') as end_date
    FROM `Order`
    WHERE OrderDate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)
    AND OrderStatus = 'Completed'
");
$weekly_report = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $conn->query("
    SELECT 
        COALESCE(COUNT(*), 0) as order_count,
        COALESCE(SUM(TotalAmount), 0) as total_revenue,
        DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY), '%M %d') as start_date,
        DATE_FORMAT(CURRENT_DATE, '%M %d') as end_date
    FROM `Order`
    WHERE OrderDate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    AND OrderStatus = 'Completed'
");
$monthly_report = $stmt->fetch(PDO::FETCH_ASSOC);

// Calculate average order values
$daily_avg = $daily_report['order_count'] > 0 ? $daily_report['total_revenue'] / $daily_report['order_count'] : 0;
$weekly_avg = $weekly_report['order_count'] > 0 ? $weekly_report['total_revenue'] / $weekly_report['order_count'] : 0;
$monthly_avg = $monthly_report['order_count'] > 0 ? $monthly_report['total_revenue'] / $monthly_report['order_count'] : 0;
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
            background: rgba(0, 0, 0, 0.05);
        }

        .admin-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            color: rgba(255, 255, 255, 0.9);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
            color: #ffffff;
        }

        .quick-actions {
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .quick-actions h4 {
            margin-bottom: 2rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .quick-action-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid #e9ecef;
            color: #495057;
            text-decoration: none;
            transition: all 0.3s ease;
            height: 100%;
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

        /* Reports Section Styles */
        .reports-section {
            margin-top: 3rem;
            padding: 2rem 0;
            background-color: #f8f9fa;
            border-radius: 15px;
            margin-bottom: 3rem;
        }

        .report-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: #ffffff;
            margin-top: 1.5rem;
        }

        .report-card .card-body {
            padding: 1.5rem;
        }

        .report-stats {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-item .stat-label {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .stat-item .stat-value {
            font-weight: 600;
            font-size: 1.1rem;
            color: #2c3e50;
        }

        /* Card Title Colors */
        .report-card .text-primary {
            color: #3498db !important;
        }

        .report-card .text-success {
            color: #2ecc71 !important;
        }

        .report-card .text-warning {
            color: #f1c40f !important;
        }

        /* Reports Title Spacing */
        .reports-section h4 {
            margin-bottom: 2rem;
            color: #2c3e50;
            font-weight: 600;
            padding-left: 1rem;
        }

        /* Report Cards Row Spacing */
        .reports-section .row {
            margin-top: 1rem;
            padding: 0 1rem;
        }

        /* Individual Report Card Spacing */
        .reports-section .col-md-4 {
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .reports-section {
                margin-top: 2rem;
                padding: 1.5rem 0;
                margin-bottom: 2rem;
            }

            .quick-actions {
                margin-top: 2rem;
                padding-top: 1.5rem;
            }

            .report-card {
                margin-bottom: 1.5rem;
            }

            .welcome-section {
                padding: 1.5rem;
                margin-bottom: 2rem;
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
                <li><a href="special_offers.php"><i class="fas fa-tags"></i>Special Offers</a></li>

                <li>
                    <a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a>
                </li>
                <li>
                    <a href="inventory.php"><i class="fas fa-box"></i> Inventory</a>
                </li>
                <li>
                    <a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a>
                </li>
                <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a>

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

                <!-- Reports Section -->
                <div class="reports-section mt-4">
                    <h4 class="mb-3"><i class="fas fa-chart-line me-2"></i>Performance Reports</h4>
                    <div class="row">
                        <!-- Daily Report -->
                        <div class="col-md-4">
                            <div class="card report-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        <i class="fas fa-calendar-day me-2"></i>Daily Report
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;"><?php echo $daily_report['report_date']; ?></small>
                                    </h5>
                                    <div class="report-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Orders</span>
                                            <span class="stat-value"><?php echo $daily_report['order_count']; ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Revenue</span>
                                            <span class="stat-value">$<?php echo number_format($daily_report['total_revenue'], 2); ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Average Order</span>
                                            <span class="stat-value">$<?php echo number_format($daily_avg, 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Weekly Report -->
                        <div class="col-md-4">
                            <div class="card report-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-success">
                                        <i class="fas fa-calendar-week me-2"></i>Weekly Report
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;"><?php echo $weekly_report['start_date']; ?> - <?php echo $weekly_report['end_date']; ?></small>
                                    </h5>
                                    <div class="report-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Orders</span>
                                            <span class="stat-value"><?php echo $weekly_report['order_count']; ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Revenue</span>
                                            <span class="stat-value">$<?php echo number_format($weekly_report['total_revenue'], 2); ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Average Order</span>
                                            <span class="stat-value">$<?php echo number_format($weekly_avg, 2); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Report -->
                        <div class="col-md-4">
                            <div class="card report-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">
                                        <i class="fas fa-calendar-alt me-2"></i>Monthly Report
                                        <small class="text-muted d-block mt-1" style="font-size: 0.8rem;"><?php echo $monthly_report['start_date']; ?> - <?php echo $monthly_report['end_date']; ?></small>
                                    </h5>
                                    <div class="report-stats">
                                        <div class="stat-item">
                                            <span class="stat-label">Orders</span>
                                            <span class="stat-value"><?php echo $monthly_report['order_count']; ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Revenue</span>
                                            <span class="stat-value">$<?php echo number_format($monthly_report['total_revenue'], 2); ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-label">Average Order</span>
                                            <span class="stat-value">$<?php echo number_format($monthly_avg, 2); ?></span>
                                        </div>
                                    </div>
                                </div>
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