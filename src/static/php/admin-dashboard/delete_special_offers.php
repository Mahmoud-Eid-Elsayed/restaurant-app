<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize response variables
$error = null;
$message = null;

// Validate input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header('Location: special_offers.php?error=' . urlencode('Invalid offer ID'));
  exit;
}

$offerId = (int) $_GET['id'];

try {
  // Start transaction
  $conn->beginTransaction();

  // Check if the offer exists and get its details
  $stmt = $conn->prepare("SELECT Description FROM SpecialOffer WHERE OfferID = ?");
  $stmt->execute([$offerId]);
  $offer = $stmt->fetch();

  if (!$offer) {
    throw new Exception('Special offer not found');
  }

  // Delete the special offer
  $stmt = $conn->prepare("DELETE FROM SpecialOffer WHERE OfferID = ?");
  $stmt->execute([$offerId]);

  // Check if the deletion was successful
  if ($stmt->rowCount() === 0) {
    throw new Exception('Failed to delete special offer');
  }

  // Commit the transaction
  $conn->commit();
  $message = 'Special offer "' . htmlspecialchars($offer['Description']) . '" has been deleted successfully';

} catch (Exception $e) {
  // Rollback the transaction on error
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }

  // Log the error for debugging
  error_log("Error deleting special offer (ID: $offerId): " . $e->getMessage());

  $error = $e->getMessage();
} catch (PDOException $e) {
  // Rollback the transaction on database error
  if ($conn->inTransaction()) {
    $conn->rollBack();
  }

  // Log the database error for debugging
  error_log("Database error while deleting special offer (ID: $offerId): " . $e->getMessage());

  // Provide a user-friendly error message
  $error = 'A database error occurred while trying to delete the special offer';
}

// Redirect back to the special offers page with appropriate message
if ($error) {
  header('Location: special_offers.php?error=' . urlencode($error));
} else {
  header('Location: special_offers.php?message=' . urlencode($message));
}
exit;
?>