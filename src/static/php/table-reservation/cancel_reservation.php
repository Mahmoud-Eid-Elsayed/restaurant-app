<?php
require '../../../../src/static/connection/db.php';

if (isset($_GET['id'])) {
    $reservation_id = $_GET['id'];

    // Check if the user confirmed the cancellation
    if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
        try {

            $conn->beginTransaction();

            $stmt = $conn->prepare("
                SELECT TableID, ReservationDate, ReservationTime
                FROM Reservation
                WHERE ReservationID = :reservation_id
            ");
            $stmt->execute([':reservation_id' => $reservation_id]);

            $reservation = $stmt->fetch();

            if ($reservation) {
                $table_id = $reservation['TableID'];
                $reservation_date = $reservation['ReservationDate'];
                $reservation_time = $reservation['ReservationTime'];

                // Update the reservation status to 'Cancelled'
                $stmt = $conn->prepare("
                    UPDATE Reservation
                    SET ReservationStatus = 'Cancelled'
                    WHERE ReservationID = :reservation_id
                ");
                $stmt->execute([':reservation_id' => $reservation_id]);

                // Commit the transaction
                $conn->commit();

                echo "Your reservation has been cancelled successfully.";
            } else {
                echo "Reservation not found.";
            }
        } catch (PDOException $e) {
            // Rollback if an error occurs
            $conn->rollBack();
            echo "Failed to cancel the reservation. Please try again later.";
        }
    } else {
        // Confirmation prompt if not confirmed
        echo '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Cancel Reservation</title>
            <link href="../../../../src/assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .confirmation-box {
                    margin: 50px auto;
                    padding: 20px;
                    max-width: 400px;
                    border: 1px solid #ccc;
                    border-radius: 10px;
                    text-align: center;
                }
                .confirmation-box h2 {
                    margin-bottom: 20px;
                }
                .confirmation-box .btn {
                    margin: 5px;
                }
            </style>
        </head>
        <body>
            <div class="confirmation-box">
                <h2>Are you sure you want to cancel this reservation?</h2>
                <a href="cancel_reservation.php?id=' . $reservation_id . '&confirm=yes" class="btn btn-danger">Yes, Cancel</a>
            </div>
        </body>
        </html>
        ';
    }
} else {
    echo "Invalid request.";
}
?>