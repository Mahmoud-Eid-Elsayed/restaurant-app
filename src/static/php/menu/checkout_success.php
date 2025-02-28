<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout Success</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
  <?php require '../includes/navbar.php'; ?>

  <div class="container mt-5">
    <div class="text-center">
      <h2>Thank you for your order! 🎉</h2>
      <p>Your order has been successfully placed.</p>
      <img src="../../../assets/images/payment/payment-checkout-success.gif" alt="Order Confirmation" class="img-fluid"
        height="100" width="150">
      <p>We will notify you when your order is ready.</p>
      <a href="menu.php" class="btn btn-primary">Back to Menu</a>
    </div>
  </div>

</body>

</html>