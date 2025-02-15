<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: reservations.php");
  exit();
}

$reservationId = $_GET['id'];

// Fetch reservation details
$stmt = $conn->prepare("
    SELECT Reservation.*, `Table`.TableNumber 
    FROM Reservation 
    INNER JOIN `Table` ON Reservation.TableID = `Table`.TableID
    WHERE ReservationID = :id
");
$stmt->execute([':id' => $reservationId]);
$reservation = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$reservation) {
  header("Location: reservations.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Reservation - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
</head>

<body>
  <div class="wrapper">
    <!-- Include the same sidebar and header as in index.php -->
    <div id="content">
      <div class="header">
        <!-- Header content -->
      </div>
      <div class="main-content">
        <h2>View Reservation</h2>
        <div class="card mb-3">
          <div class="card-body">
            <h5 class="card-title">Reservation Details</h5>
            <p><strong>Reservation ID:</strong> <?php echo $reservation['ReservationID']; ?></p>
            <p><strong>Customer Name:</strong> <?php echo $reservation['CustomerName']; ?></p>
            <p><strong>Table Number:</strong> <?php echo $reservation['TableNumber']; ?></p>
            <p><strong>Reservation Date:</strong> <?php echo $reservation['ReservationDate']; ?></p>
            <p><strong>Reservation Time:</strong> <?php echo $reservation['ReservationTime']; ?></p>
            <p><strong>Number of Guests:</strong> <?php echo $reservation['NumberOfGuests']; ?></p>
            <p><strong>Status:</strong> <?php echo $reservation['ReservationStatus']; ?></p>
            <p><strong>Notes:</strong> <?php echo $reservation['Notes']; ?></p>
          </div>
        </div>
        <a href="reservations.php" class="btn btn-secondary">Back to Reservations</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>