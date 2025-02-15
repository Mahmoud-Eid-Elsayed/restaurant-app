<?php
require_once '../../connection/db.php';

// Fetch all reservations with table numbers
$stmt = $conn->query("
    SELECT Reservation.*, `Table`.TableNumber 
    FROM Reservation 
    INNER JOIN `Table` ON Reservation.TableID = `Table`.TableID
");
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
</head>

<body>
  <div class="wrapper">
    <!-- Include the same sidebar and header as in index.php -->
    <div id="content">
      <div class="header">
        <!-- Header content -->
      </div>
      <div class="main-content">
        <h2>Manage Reservations</h2>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Customer Name</th>
              <th>Table Number</th>
              <th>Reservation Date</th>
              <th>Reservation Time</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($reservations as $reservation): ?>
              <tr>
                <td><?php echo $reservation['ReservationID']; ?></td>
                <td><?php echo $reservation['CustomerName']; ?></td>
                <td><?php echo $reservation['TableNumber']; ?></td>
                <td><?php echo $reservation['ReservationDate']; ?></td>
                <td><?php echo $reservation['ReservationTime']; ?></td>
                <td><?php echo $reservation['ReservationStatus']; ?></td>
                <td>
                  <a href="view_reservation.php?id=<?php echo $reservation['ReservationID']; ?>"
                    class="btn btn-info btn-sm">View</a>
                  <a href="edit_reservation.php?id=<?php echo $reservation['ReservationID']; ?>"
                    class="btn btn-warning btn-sm">Edit</a>
                  <a href="delete_reservation.php?id=<?php echo $reservation['ReservationID']; ?>"
                    class="btn btn-danger btn-sm">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>