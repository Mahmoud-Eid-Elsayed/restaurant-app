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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $status = $_POST['status'];

  $stmt = $conn->prepare("
        UPDATE Reservation 
        SET ReservationStatus = :status
        WHERE ReservationID = :id
    ");
  $stmt->execute([
    ':status' => $status,
    ':id' => $reservationId
  ]);

  header("Location: reservations.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Reservation - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
</head>

<body>
  <div class="wrapper">
    
    <div id="content">
      <div class="header">
        
      </div>
      <div class="main-content">
        <h2>Edit Reservation</h2>
        <form method="POST">
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
              <option value="Pending" <?php echo $reservation['ReservationStatus'] == 'Pending' ? 'selected' : ''; ?>>
                Pending</option>
              <option value="Confirmed" <?php echo $reservation['ReservationStatus'] == 'Confirmed' ? 'selected' : ''; ?>>
                Confirmed</option>
              <option value="Cancelled" <?php echo $reservation['ReservationStatus'] == 'Cancelled' ? 'selected' : ''; ?>>
                Cancelled</option>
              <option value="Completed" <?php echo $reservation['ReservationStatus'] == 'Completed' ? 'selected' : ''; ?>>
                Completed</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Update Reservation</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>