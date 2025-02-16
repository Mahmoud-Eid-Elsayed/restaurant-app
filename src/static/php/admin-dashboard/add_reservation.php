<?php
require_once '../../connection/db.php';

// Fetch all tables
$stmt = $conn->query("SELECT * FROM `Table`");
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $customerName = $_POST['customerName'];
  $customerEmail = $_POST['customerEmail'];
  $customerPhone = $_POST['customerPhone'];
  $tableID = $_POST['tableID'];
  $reservationDate = $_POST['reservationDate'];
  $reservationTime = $_POST['reservationTime'];
  $numberOfGuests = $_POST['numberOfGuests'];
  $notes = $_POST['notes'];

  $stmt = $conn->prepare("
        INSERT INTO Reservation (CustomerName, CustomerEmail, CustomerPhone, TableID, ReservationDate, ReservationTime, NumberOfGuests, Notes)
        VALUES (:customerName, :customerEmail, :customerPhone, :tableID, :reservationDate, :reservationTime, :numberOfGuests, :notes)
    ");
  $stmt->execute([
    ':customerName' => $customerName,
    ':customerEmail' => $customerEmail,
    ':customerPhone' => $customerPhone,
    ':tableID' => $tableID,
    ':reservationDate' => $reservationDate,
    ':reservationTime' => $reservationTime,
    ':numberOfGuests' => $numberOfGuests,
    ':notes' => $notes
  ]);

  header("Location: ../reservations.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Reservation - ELCHEF</title>
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
        <h2>Add Reservation</h2>
        <form method="POST">
          <div class="mb-3">
            <label for="customerName" class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customerName" name="customerName" required>
          </div>
          <div class="mb-3">
            <label for="customerEmail" class="form-label">Customer Email</label>
            <input type="email" class="form-control" id="customerEmail" name="customerEmail" required>
          </div>
          <div class="mb-3">
            <label for="customerPhone" class="form-label">Customer Phone</label>
            <input type="text" class="form-control" id="customerPhone" name="customerPhone" required>
          </div>
          <div class="mb-3">
            <label for="tableID" class="form-label">Table</label>
            <select class="form-control" id="tableID" name="tableID" required>
              <?php foreach ($tables as $table): ?>
                <option value="<?php echo $table['TableID']; ?>">Table <?php echo $table['TableNumber']; ?> (Capacity:
                  <?php echo $table['Capacity']; ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="reservationDate" class="form-label">Reservation Date</label>
            <input type="date" class="form-control" id="reservationDate" name="reservationDate" required>
          </div>
          <div class="mb-3">
            <label for="reservationTime" class="form-label">Reservation Time</label>
            <input type="time" class="form-control" id="reservationTime" name="reservationTime" required>
          </div>
          <div class="mb-3">
            <label for="numberOfGuests" class="form-label">Number of Guests</label>
            <input type="number" class="form-control" id="numberOfGuests" name="numberOfGuests" required>
          </div>
          <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Add Reservation</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>