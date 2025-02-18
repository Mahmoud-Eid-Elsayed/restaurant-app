<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize variables
$error = null;
$reservation = null;

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: reservations.php?error=" . urlencode('Invalid reservation ID'));
    exit();
}

$reservationId = (int) $_GET['id'];

try {
    // Fetch reservation details with table information
    $stmt = $conn->prepare("
        SELECT 
            r.*,
            t.TableNumber,
            t.Capacity as TableCapacity
        FROM Reservation r
        INNER JOIN `Table` t ON r.TableID = t.TableID
        WHERE r.ReservationID = ?
    ");
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        throw new Exception('Reservation not found');
    }

    // Fetch reservation history
    $historyStmt = $conn->prepare("
        SELECT *
        FROM ReservationHistory
        WHERE ReservationID = ?
        ORDER BY CreatedAt DESC
    ");
    $historyStmt->execute([$reservationId]);
    $reservationHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $error = $e->getMessage();
    error_log("Error in view_reservation.php: " . $e->getMessage());
} catch (PDOException $e) {
    $error = "Database error occurred";
    error_log("Database error in view_reservation.php: " . $e->getMessage());
}

// Define status colors for consistency
$statusColors = [
    'Pending' => 'warning',
    'Confirmed' => 'success',
    'Cancelled' => 'danger',
    'Completed' => 'info'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reservation #<?php echo $reservationId; ?> - ELCHEF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
    <style>
        .reservation-details {
            background-color: #f8f9fa;
            border-radius: 0.25rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .status-badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .timeline {
            position: relative;
            padding: 1rem 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-marker {
            position: absolute;
            left: 0;
            top: 0;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #f8f9fa;
            border: 2px solid #dee2e6;
            text-align: center;
            line-height: 20px;
        }

        .timeline-marker i {
            font-size: 12px;
        }

        .timeline-content {
            position: relative;
            padding-bottom: 1rem;
            border-bottom: 1px dashed #dee2e6;
        }

        .timeline-item:last-child .timeline-content {
            border-bottom: none;
            padding-bottom: 0;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>ELCHEF Admin</h3>
            </div>
            <ul class="list-unstyled components">
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a></li>
                <li><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li class="active"><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <button type="button" id="sidebarToggle" class="btn btn-info">
                <i class="fas fa-bars"></i>
            </button>

            <div class="main-content">
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($error); ?>
                        <br>
                        <a href="reservations.php" class="btn btn-secondary mt-2">Back to Reservations</a>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>
                            Reservation #<?php echo htmlspecialchars($reservationId); ?>
                            <span class="badge status-badge bg-<?php
                            echo $statusColors[$reservation['ReservationStatus']] ?? 'secondary';
                            ?>">
                                <?php echo htmlspecialchars($reservation['ReservationStatus']); ?>
                            </span>
                        </h2>
                        <div class="btn-group">
                            <a href="reservations.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Reservations
                            </a>
                            <?php if ($reservation['ReservationStatus'] !== 'Cancelled' && $reservation['ReservationStatus'] !== 'Completed'): ?>
                                <a href="edit_reservation.php?id=<?php echo $reservationId; ?>" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Reservation
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-info-circle"></i> Reservation Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="reservation-details">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Customer Name</h6>
                                        <p class="mb-0 h5"><?php echo htmlspecialchars($reservation['CustomerName']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Contact Number</h6>
                                        <p class="mb-0 h5">
                                            <?php if (!empty($reservation['ContactNumber'])): ?>
                                                <a href="tel:<?php echo htmlspecialchars($reservation['ContactNumber']); ?>"
                                                    class="text-decoration-none">
                                                    <i class="fas fa-phone-alt me-1"></i>
                                                    <?php echo htmlspecialchars($reservation['ContactNumber']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No contact number provided</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Reservation Date</h6>
                                        <p class="mb-0 h5">
                                            <?php
                                            $date = new DateTime($reservation['ReservationDate']);
                                            echo $date->format('F d, Y');
                                            ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Reservation Time</h6>
                                        <p class="mb-0 h5">
                                            <?php
                                            $time = new DateTime($reservation['ReservationTime']);
                                            echo $time->format('h:i A');
                                            ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Table Number</h6>
                                        <p class="mb-0 h5">
                                            Table #<?php echo htmlspecialchars($reservation['TableNumber']); ?>
                                            <small class="text-muted">
                                                (Capacity: <?php echo htmlspecialchars($reservation['TableCapacity']); ?>
                                                persons)
                                            </small>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Number of Guests</h6>
                                        <p class="mb-0 h5"><?php echo htmlspecialchars($reservation['NumberOfGuests']); ?>
                                            persons</p>
                                    </div>
                                </div>
                                <?php if (!empty($reservation['Notes'])): ?>
                                    <div class="row">
                                        <div class="col-12">
                                            <h6 class="text-muted mb-1">Special Notes</h6>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($reservation['Notes'])); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Reservation Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-clock"></i> Reservation Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php if (!empty($reservationHistory)):
                                    foreach ($reservationHistory as $event):
                                        $eventDate = new DateTime($event['CreatedAt']);
                                        // Determine icon and color based on status
                                        $icon = 'fa-circle-info';
                                        $color = 'text-primary';

                                        if (stripos($event['Notes'], 'created') !== false) {
                                            $icon = 'fa-plus-circle';
                                            $color = 'text-success';
                                        } elseif (stripos($event['Status'], 'Cancelled') !== false) {
                                            $icon = 'fa-times-circle';
                                            $color = 'text-danger';
                                        } elseif (stripos($event['Status'], 'Confirmed') !== false) {
                                            $icon = 'fa-check-circle';
                                            $color = 'text-success';
                                        } elseif (stripos($event['Status'], 'Completed') !== false) {
                                            $icon = 'fa-flag-checkered';
                                            $color = 'text-info';
                                        }
                                        ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker">
                                                <i class="fas <?php echo $icon; ?> <?php echo $color; ?>"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <small class="text-muted d-block">
                                                    <?php echo $eventDate->format('M d, Y h:i A'); ?>
                                                </small>
                                                <p class="mb-0">
                                                    <?php echo htmlspecialchars($event['Notes']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                else: ?>
                                    <div class="text-muted text-center py-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        No timeline events available
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
</body>

</html>