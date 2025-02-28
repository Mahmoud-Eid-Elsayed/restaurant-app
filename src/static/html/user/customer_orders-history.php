<?php

require_once __DIR__ . '../../../connection/db.php';

session_start();


if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

// Fetch the order history and reservations for the logged-in user
$user_id = $_SESSION['user_id'];

try {
  // Fetch order history (only show the latest status for each order)
  $order_query = "
        SELECT o.OrderID, o.OrderDate, o.OrderStatus, o.TotalAmount, o.Notes
        FROM `order` o
        WHERE o.CustomerID = :user_id
        AND o.OrderStatus IN ('Pending','Processing','Completed','Cancelled','Refunded') -- Filter by specific statuses
        ORDER BY o.OrderDate DESC
    ";
  $order_stmt = $conn->prepare($order_query);
  $order_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $order_stmt->execute();
  $order_history = $order_stmt->fetchAll(PDO::FETCH_ASSOC);

  // Fetch table reservations
  $reservation_query = "
        SELECT r.ReservationID, r.TableID, r.CustomerName, r.ReservationDate, 
               r.ReservationTime, r.NumberOfGuests, r.ReservationStatus, r.Notes
        FROM reservation r
        WHERE r.CustomerID = :user_id
        ORDER BY r.ReservationDate DESC, r.ReservationTime DESC
    ";
  $reservation_stmt = $conn->prepare($reservation_query);
  $reservation_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $reservation_stmt->execute();
  $reservations = $reservation_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order History</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h1 class="mb-4">Order History</h1>

    <!-- Order History -->
    <h2>Past Orders</h2>
    <?php if (count($order_history) > 0): ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Total Amount</th>
            <th>Notes</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($order_history as $order): ?>
            <tr>
              <td><?php echo htmlspecialchars($order['OrderID']); ?></td>
              <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
              <td><?php echo htmlspecialchars($order['OrderStatus']); ?></td>
              <td><?php echo htmlspecialchars($order['TotalAmount']); ?></td>
              <td><?php echo htmlspecialchars($order['Notes']); ?></td>
              <td>
                <?php if (in_array($order['OrderStatus'], ['Completed', 'Refunded'])): ?>
                  <a href="submit_feedback.php?order_id=<?php echo $order['OrderID']; ?>"
                    class="btn btn-primary btn-sm">Submit Feedback</a>
                  <a href="reorder.php?order_id=<?php echo $order['OrderID']; ?>" class="btn btn-success btn-sm">Reorder</a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>

        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info">No past orders found.</div>
    <?php endif; ?>

    <!-- Table Reservations -->
    <h2 class="mt-5">Booked Tables</h2>
    <?php if (count($reservations) > 0): ?>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Reservation ID</th>
            <th>Table ID</th>
            <th>Customer Name</th>
            <th>Reservation Date</th>
            <th>Reservation Time</th>
            <th>Number of Guests</th>
            <th>Status</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($reservations as $reservation): ?>
            <tr>
              <td><?php echo htmlspecialchars($reservation['ReservationID']); ?></td>
              <td><?php echo htmlspecialchars($reservation['TableID']); ?></td>
              <td><?php echo htmlspecialchars($reservation['CustomerName']); ?></td>
              <td><?php echo htmlspecialchars($reservation['ReservationDate']); ?></td>
              <td><?php echo htmlspecialchars($reservation['ReservationTime']); ?></td>
              <td><?php echo htmlspecialchars($reservation['NumberOfGuests']); ?></td>
              <td><?php echo htmlspecialchars($reservation['ReservationStatus']); ?></td>
              <td><?php echo htmlspecialchars($reservation['Notes']); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info">No table reservations found.</div>
    <?php endif; ?>
  </div>

  <!-- Bootstrap JS (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>