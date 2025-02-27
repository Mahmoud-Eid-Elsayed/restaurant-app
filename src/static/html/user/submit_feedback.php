<?php
require_once __DIR__ . '../../../connection/db.php';
require_once "../../php/user/user.php";

session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit();
}

if (!isset($_GET['order_id'])) {
  header('Location: orders-history.php');
  exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

try {
  $order_query = "SELECT * FROM `order` WHERE OrderID = :order_id AND CustomerID = :user_id";
  $order_stmt = $conn->prepare($order_query);
  $order_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
  $order_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $order_stmt->execute();
  $order = $order_stmt->fetch();

  if (!$order) {
    die("Order not found or does not belong to you.");
  }
} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $rating = $_POST['rating'];
  $comment = $_POST['comment'];

  if ($rating < 1 || $rating > 5) {
    $error = "Invalid rating. Please provide a rating between 1 and 5.";
  } else {
    try {
      $feedback_query = "
                INSERT INTO review (CustomerID, OrderID, Rating, Comment, ReviewDate)
                VALUES (:customer_id, :order_id, :rating, :comment, NOW())
            ";
      $feedback_stmt = $conn->prepare($feedback_query);
      $feedback_stmt->bindParam(':customer_id', $user_id, PDO::PARAM_INT);
      $feedback_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
      $feedback_stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
      $feedback_stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
      $feedback_stmt->execute();

      $success = "Feedback submitted successfully!";
    } catch (PDOException $e) {
      $error = "Database error: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Feedback</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="d-flex">
    <?php include 'sidebar.php' ?>
    <div class="container mt-5">
      <h1 class="mb-4">Submit Feedback for Order #<?php echo htmlspecialchars($order_id); ?></h1>

      <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>

      <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="rating" class="form-label">Rating (1-5)</label>
          <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
        </div>
        <div class="mb-3">
          <label for="comment" class="form-label">Comment</label>
          <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit Feedback</button>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>