<?php
require_once '../../connection/db.php';

// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Initialize variables
$error = null;
$message = null;

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$search_query = $_GET['search'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

try {
    // Build the query with filters
    $query = "
        SELECT 
            r.*,
            t.TableNumber,
            t.Capacity as TableCapacity
        FROM Reservation r
        INNER JOIN `Table` t ON r.TableID = t.TableID
        WHERE 1=1
    ";

    $params = [];

    // Apply status filter
    if ($status_filter !== 'all') {
        $query .= " AND r.ReservationStatus = ?";
        $params[] = $status_filter;
    }

    // Apply search filter
    if ($search_query) {
        $query .= " AND (
            r.CustomerName LIKE ? OR 
            r.ContactNumber LIKE ? OR
            r.Notes LIKE ?
        )";
        $search_term = "%$search_query%";
        $params = array_merge($params, array_fill(0, 3, $search_term));
    }

    // Apply date range filter
    if ($date_from) {
        $query .= " AND r.ReservationDate >= ?";
        $params[] = $date_from;
    }
    if ($date_to) {
        $query .= " AND r.ReservationDate <= ?";
        $params[] = $date_to;
    }

    $query .= " ORDER BY r.ReservationDate DESC, r.ReservationTime DESC";

    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get reservation status counts for the filter buttons
    $statusCounts = $conn->query("
        SELECT 
            ReservationStatus,
            COUNT(*) as count
    FROM Reservation 
        GROUP BY ReservationStatus
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

} catch (PDOException $e) {
    error_log("Error in reservations.php: " . $e->getMessage());
    $error = "An error occurred while fetching reservations. Please try again later.";
}

// Define status colors for consistency
$statusColors = [
    'Pending' => 'warning',
    'Confirmed' => 'success',
    'Cancelled' => 'danger',
    'Completed' => 'info'
];

// Function to safely format date/time
function formatDateTime($date, $time)
{
    try {
        $dateObj = new DateTime($date);
        $timeObj = new DateTime($time);
        return [
            'date' => $dateObj->format('M d, Y'),
            'time' => $timeObj->format('h:i A')
        ];
    } catch (Exception $e) {
        error_log("Date formatting error: " . $e->getMessage());
        return [
            'date' => 'Invalid date',
            'time' => 'Invalid time'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations - ELCHEF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
    <style>
        .status-badge {
            min-width: 100px;
            text-align: center;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }

        .contact-link {
            color: inherit;
            text-decoration: none;
        }

        .contact-link:hover {
            color: #0d6efd;
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
                <li>
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
                <li class="active">
                    <a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a>
                </li>
                <li>
                    <a href="inventory.php"><i class="fas fa-box"></i> Inventory</a>
                </li>
                <li>
                    <a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <button type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="main-content">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_GET['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Reservations</h2>
                    <button type="button" class="btn btn-outline-secondary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>

                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search"
                                    value="<?php echo htmlspecialchars($search_query); ?>"
                                    placeholder="Search reservations...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" name="date_from"
                                    value="<?php echo htmlspecialchars($date_from); ?>" placeholder="From date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" name="date_to"
                                    value="<?php echo htmlspecialchars($date_to); ?>" placeholder="To date">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Apply
                            </button>
                        </div>
                    </form>

                    <!-- Status Filter Buttons -->
                    <div class="mt-3">
                        <div class="btn-group">
                            <a href="?status=all"
                                class="btn btn-outline-secondary <?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                                All (<?php echo array_sum($statusCounts); ?>)
                            </a>
                            <?php foreach ($statusColors as $status => $color): ?>
                                <a href="?status=<?php echo $status; ?>"
                                    class="btn btn-outline-<?php echo $color; ?> <?php echo $status_filter === $status ? 'active' : ''; ?>">
                                    <?php echo $status; ?> (<?php echo $statusCounts[$status] ?? 0; ?>)
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <?php if (!empty($reservations)): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Contact</th>
                                    <th>Date & Time</th>
                                    <th>Table</th>
                                    <th>Guests</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $reservation): ?>
                                    <?php $datetime = formatDateTime($reservation['ReservationDate'], $reservation['ReservationTime']); ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($reservation['ReservationID']); ?></td>
                                        <td><?php echo htmlspecialchars($reservation['CustomerName']); ?></td>
                                        <td>
                                            <?php if (!empty($reservation['ContactNumber'])): ?>
                                                <a href="tel:<?php echo htmlspecialchars($reservation['ContactNumber']); ?>"
                                                    class="contact-link">
                                                    <i class="fas fa-phone-alt me-1"></i>
                                                    <?php echo htmlspecialchars($reservation['ContactNumber']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">
                                                    <i class="fas fa-phone-slash me-1"></i>
                                                    No contact number
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php echo $datetime['date']; ?><br>
                                            <small class="text-muted"><?php echo $datetime['time']; ?></small>
                                        </td>
                                        <td>
                                            Table #<?php echo htmlspecialchars($reservation['TableNumber']); ?>
                                            <br>
                                            <small class="text-muted">
                                                (Capacity: <?php echo htmlspecialchars($reservation['TableCapacity']); ?>)
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            <?php echo htmlspecialchars($reservation['NumberOfGuests']); ?>
                                        </td>
                                        <td>
                                            <span class="badge status-badge bg-<?php
                                            echo $statusColors[$reservation['ReservationStatus']] ?? 'secondary';
                                            ?>">
                                                <?php echo htmlspecialchars($reservation['ReservationStatus']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="view_reservation.php?id=<?php echo $reservation['ReservationID']; ?>"
                                                    class="btn btn-info btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($reservation['ReservationStatus'] !== 'Cancelled' && $reservation['ReservationStatus'] !== 'Completed'): ?>
                                                    <a href="edit_reservation.php?id=<?php echo $reservation['ReservationID']; ?>"
                                                        class="btn btn-warning btn-sm" title="Edit Reservation">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button"
                                                        onclick="showCancelModal(<?php echo $reservation['ReservationID']; ?>, '<?php echo htmlspecialchars(addslashes($reservation['CustomerName'])); ?>', '<?php echo htmlspecialchars(addslashes($reservation['TableNumber'])); ?>')"
                                                        class="btn btn-danger btn-sm" title="Cancel Reservation">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        No reservations found matching your criteria.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cancel Reservation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Confirm Reservation Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel the reservation for:</p>
                    <p class="mb-2">
                        <strong>Customer:</strong> <span id="customerName"></span><br>
                        <strong>Table:</strong> #<span id="tableNumber"></span>
                    </p>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-triangle"></i> This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Reservation</button>
                    <button type="button" class="btn btn-danger" id="confirmCancel">
                        <i class="fas fa-times"></i> Cancel Reservation
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
    <script>
        let cancelModal;
        let reservationToCancel = null;

        document.addEventListener('DOMContentLoaded', function () {
            // Initialize modal
            cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));

            // Set up cancel confirmation button
            document.getElementById('confirmCancel').addEventListener('click', function () {
                if (reservationToCancel) {
                    window.location.href = `delete_reservation.php?id=${reservationToCancel.id}&token=${Date.now()}`;
                }
                cancelModal.hide();
            });

            // Auto-close alerts after 5 seconds
            setTimeout(function () {
                document.querySelectorAll('.alert').forEach(function (alert) {
                    if (alert && typeof bootstrap !== 'undefined') {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);
        });

        function showCancelModal(reservationId, customerName, tableNumber) {
            reservationToCancel = {
                id: reservationId
            };
            document.getElementById('customerName').textContent = customerName;
            document.getElementById('tableNumber').textContent = tableNumber;
            cancelModal.show();
        }
    </script>
</body>

</html>