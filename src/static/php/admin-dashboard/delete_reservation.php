<?php
require_once '../../connection/db.php';

// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Initialize response variables
$error = null;
$message = null;

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: reservations.php?error=' . urlencode('Invalid reservation ID'));
    exit;
}

$reservationId = (int)$_GET['id'];

try {
    // Start transaction
    $conn->beginTransaction();

    // Check if the reservation exists and get its details
    $stmt = $conn->prepare("
        SELECT r.*, t.TableNumber 
        FROM Reservation r
        INNER JOIN `Table` t ON r.TableID = t.TableID
        WHERE r.ReservationID = ?
    ");
    $stmt->execute([$reservationId]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        throw new Exception('Reservation not found');
    }

    // Check if the reservation can be cancelled
    if ($reservation['ReservationStatus'] === 'Cancelled') {
        throw new Exception('Reservation is already cancelled');
    }

    if ($reservation['ReservationStatus'] === 'Completed') {
        throw new Exception('Cannot cancel a completed reservation');
    }

    // Update the reservation status to Cancelled
    $stmt = $conn->prepare("
        UPDATE Reservation 
        SET ReservationStatus = 'Cancelled',
            LastModified = CURRENT_TIMESTAMP
        WHERE ReservationID = ?
    ");
    $stmt->execute([$reservationId]);

    // Check if the update was successful
    if ($stmt->rowCount() === 0) {
        throw new Exception('Failed to cancel reservation');
    }

    // Add to reservation history
    $stmt = $conn->prepare("
        INSERT INTO ReservationHistory (ReservationID, Status, Notes)
        VALUES (?, 'Cancelled', 'Reservation cancelled by admin')
    ");
    $stmt->execute([$reservationId]);

    // Commit the transaction
    $conn->commit();

    // Create success message
    $message = sprintf(
        'Reservation #%d for %s (Table #%s) has been cancelled successfully',
        $reservationId,
        $reservation['CustomerName'],
        $reservation['TableNumber']
    );

} catch (Exception $e) {
    // Rollback the transaction on error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log the error for debugging
    error_log("Error cancelling reservation (ID: $reservationId): " . $e->getMessage());
    
    $error = $e->getMessage();
} catch (PDOException $e) {
    // Rollback the transaction on database error
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    
    // Log the database error for debugging
    error_log("Database error while cancelling reservation (ID: $reservationId): " . $e->getMessage());
    
    $error = 'A database error occurred while trying to cancel the reservation';
}

// Redirect back to the reservations page with appropriate message
if ($error) {
    header('Location: reservations.php?error=' . urlencode($error));
} else {
    header('Location: reservations.php?message=' . urlencode($message));
}
exit;
?>