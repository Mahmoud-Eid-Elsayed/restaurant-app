<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';

if (!isset($_SESSION['cart'])) {
  $_SESSION['cart'] = [];
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php?message=Cart cleared successfully');
    exit;
  } elseif (isset($_POST['id'], $_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if (isset($_SESSION['cart'][$id])) {
      if ($action == 'increase') {
        $_SESSION['cart'][$id]['quantity']++;
      } elseif ($action == 'decrease') {
        if ($_SESSION['cart'][$id]['quantity'] > 1) {
          $_SESSION['cart'][$id]['quantity']--;
        } else {
          unset($_SESSION['cart'][$id]);
        }
      }
    }
    header('Location: cart.php');
    exit;
  } elseif (isset($_POST['remove_id'])) {
    $remove_id = $_POST['remove_id'];
    if (isset($_SESSION['cart'][$remove_id])) {
      unset($_SESSION['cart'][$remove_id]);
    }
    header('Location: cart.php?message=Item removed from cart');
    exit;
  }
}

// Calculate total items and price
$totalItems = 0;
$totalPrice = 0;
foreach ($_SESSION['cart'] as $item) {
  $totalItems += $item['quantity'];
  $totalPrice += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart - ELCHEF</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary-color: #e67e22;
      --secondary-color: #2c3e50;
      --accent-color: #f39c12;
      --text-dark: #2d3436;
      --text-light: #636e72;
      --white: #ffffff;
      --danger: #e74c3c;
      --success: #2ecc71;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fa;
      color: var(--text-dark);
    }

    .cart-header {
      background: linear-gradient(135deg, var(--secondary-color) 0%, #1a252f 100%);
      padding: 2rem 0;
      color: var(--white);
      margin-bottom: 2rem;
    }

    .cart-container {
      background: var(--white);
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin-bottom: 2rem;
    }

    .cart-item {
      background: #fff;
      border-radius: 10px;
      padding: 1rem;
      margin-bottom: 1rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
    }

    .cart-item:hover {
      transform: translateY(-5px);
    }

    .cart-item img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 8px;
    }

    .quantity-control {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .quantity-btn {
      background: var(--white);
      border: 2px solid var(--primary-color);
      color: var(--primary-color);
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .quantity-btn:hover {
      background: var(--primary-color);
      color: var(--white);
    }

    .quantity-number {
      font-size: 1.1rem;
      font-weight: 600;
      min-width: 40px;
      text-align: center;
    }

    .item-price {
      font-size: 1.1rem;
      font-weight: 600;
      color: var(--primary-color);
    }

    .remove-btn {
      color: var(--danger);
      background: none;
      border: none;
      font-size: 1.2rem;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .remove-btn:hover {
      transform: scale(1.1);
    }

    .cart-summary {
      background: var(--white);
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 1rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #eee;
    }

    .summary-total {
      font-size: 1.25rem;
      font-weight: 700;
      color: var(--primary-color);
    }

    .btn-checkout {
      background: var(--primary-color);
      color: var(--white);
      border: none;
      padding: 1rem 2rem;
      border-radius: 10px;
      font-weight: 600;
      width: 100%;
      margin-top: 1rem;
      transition: all 0.3s ease;
    }

    .btn-checkout:hover {
      background: #d35400;
      transform: translateY(-2px);
    }

    .btn-continue-shopping {
      background: var(--secondary-color);
      color: var(--white);
      border: none;
      padding: 1rem 2rem;
      border-radius: 10px;
      font-weight: 600;
      width: 100%;
      margin-top: 1rem;
      transition: all 0.3s ease;
    }

    .btn-continue-shopping:hover {
      background: #1a252f;
      transform: translateY(-2px);
    }

    .empty-cart {
      text-align: center;
      padding: 3rem;
    }

    .empty-cart i {
      font-size: 4rem;
      color: var(--text-light);
      margin-bottom: 1rem;
    }

    .alert {
      border-radius: 10px;
      margin-bottom: 1rem;
    }
  </style>
</head>

<body>
  <?php require_once '../includes/navbar.php'; ?>

  <div class="cart-header">
    <div class="container">
      <h1 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Your Cart</h1>
      <p class="lead mb-0">Review and manage your selected items</p>
    </div>
  </div>

  <div class="container mb-5">
    <?php if (isset($_GET['message'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($_GET['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <?php if (empty($_SESSION['cart'])): ?>
      <div class="cart-container empty-cart">
        <i class="fas fa-shopping-cart mb-4"></i>
        <h3>Your cart is empty</h3>
        <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
        <a href="menu.php" class="btn btn-continue-shopping">
          <i class="fas fa-utensils me-2"></i>Browse Menu
        </a>
      </div>
    <?php else: ?>
      <div class="row">
        <div class="col-lg-8">
          <div class="cart-container">
            <?php foreach ($_SESSION['cart'] as $id => $item): ?>
              <div class="cart-item">
                <div class="row align-items-center">
                  <div class="col-md-2">
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>"
                      class="img-fluid">
                  </div>
                  <div class="col-md-4">
                    <h5 class="mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                    <div class="item-price">$<?= number_format($item['price'], 2) ?></div>
                  </div>
                  <div class="col-md-3">
                    <form method="POST" class="quantity-control">
                      <input type="hidden" name="id" value="<?= $id ?>">
                      <button type="submit" name="action" value="decrease" class="quantity-btn">-</button>
                      <span class="quantity-number"><?= $item['quantity'] ?></span>
                      <button type="submit" name="action" value="increase" class="quantity-btn">+</button>
                    </form>
                  </div>
                  <div class="col-md-2">
                    <div class="item-price">
                      $<?= number_format($item['price'] * $item['quantity'], 2) ?>
                    </div>
                  </div>
                  <div class="col-md-1">
                    <form method="POST" style="display: inline;">
                      <input type="hidden" name="remove_id" value="<?= $id ?>">
                      <button type="submit" class="remove-btn" title="Remove item">
                        <i class="fas fa-times"></i>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="cart-summary">
            <h4 class="mb-4">Order Summary</h4>
            <div class="summary-item">
              <span>Total Items:</span>
              <span><?= $totalItems ?></span>
            </div>
            <div class="summary-item">
              <span>Subtotal:</span>
              <span>$<?= number_format($totalPrice, 2) ?></span>
            </div>
            <div class="summary-item">
              <span>Delivery Fee:</span>
              <span>$5.00</span>
            </div>
            <div class="summary-total">
              <span>Total:</span>
              <span>$<?= number_format($totalPrice + 5, 2) ?></span>
            </div>
            <a href="checkout.php" class="btn btn-checkout">
              <i class="fas fa-lock me-2"></i>Proceed to Checkout
            </a>
            <a href="menu.php" class="btn btn-continue-shopping">
              <i class="fas fa-utensils me-2"></i>Continue Shopping
            </a>
            <form method="POST" class="mt-3">
              <button type="submit" name="clear_cart" class="btn btn-outline-danger w-100">
                <i class="fas fa-trash me-2"></i>Clear Cart
              </button>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
      setTimeout(function () {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
          const bsAlert = new bootstrap.Alert(alert);
          bsAlert.close();
        });
      }, 5000);
    });
  </script>
</body>

</html>