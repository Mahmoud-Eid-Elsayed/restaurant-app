<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: reservations.php");
  exit();
}

$reservationId = $_GET['id'];


$stmt = $conn->prepare("DELETE FROM Reservation WHERE ReservationID = :id");
$stmt->execute([':id' => $reservationId]);

header("Location: reservations.php");
exit();
?>