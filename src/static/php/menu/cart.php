<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';
require '../includes/navbar.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['clear_cart'])) {
        // Clear the cart
        unset($_SESSION['cart']);
    } elseif (isset($_POST['id'], $_POST['action'])) {
        // Update item quantity
        $id = $_POST['id'];
        $action = $_POST['action'];

        if ($action == 'increase') {
            $_SESSION['cart'][$id]['quantity']++;
        } elseif ($action == 'decrease' && $_SESSION['cart'][$id]['quantity'] > 1) {
            $_SESSION['cart'][$id]['quantity']--;
        }
    } elseif (isset($_POST['remove_id'])) {
        // Remove an item from the cart
        $remove_id = $_POST['remove_id'];
        unset($_SESSION['cart'][$remove_id]);
    }
}

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #ECF0F1;
        }

        .navbar1 {
            background-color: #2C3E50;
            padding: 15px;
            border-radius: 10px;
            z-index: 1000;
            text-align: center;
            position: sticky;
            top: 80px;
        }

        .navbar1 a {
            color: white;
            font-weight: bold;
            margin-right: 35px;
            text-decoration: none;
        }

        .navbar1 a:hover {
            color: #E74C3C;
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s, box-shadow 0.3s;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
        }

        .price {
            font-size: 1.1rem;
            color: #E74C3C;
            font-weight: bold;
        }

        .btn-add {
            background-color: #27AE60;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            transition: background 0.3s;
        }

        .btn-add:hover {
            background-color: #219150;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #2C3E50;
        }

        .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            font-size: 12px;
            border-radius: 50%;
            padding: 3px 6px;
        }

        .pagination a.active {
            font-weight: bold;
            color: #E74C3C;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Shopping Cart 🛒</h2>

        <?php if (empty($_SESSION['cart'])): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalPrice = 0;
                    foreach ($_SESSION['cart'] as $id => $item):
                        // Ensure all required keys exist
                        if (!isset($item['name'], $item['price'], $item['image'], $item['quantity'])) {
                            continue; // Skip invalid items
                        }

                        $subtotal = $item['price'] * $item['quantity'];
                        $totalPrice += $subtotal;
                        ?>
                        <tr>
                            <td><img src="<?php echo $item['image']; ?>" width="60" height="60" alt="Item"></td>
                            <td><?php echo $item['name']; ?></td>
                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <button type="submit" name="action" value="decrease"
                                        class="btn btn-sm btn-danger">-</button>
                                    <span class="mx-2"><?php echo $item['quantity']; ?></span>
                                    <button type="submit" name="action" value="increase"
                                        class="btn btn-sm btn-success">+</button>
                                </form>
                            </td>
                            <td>$<?php echo number_format($subtotal, 2); ?></td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="remove_id" value="<?php echo $id; ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Total: $<?php echo number_format($totalPrice, 2); ?></h3>
            <a href="checkout.php" class="btn btn-primary">Checkout</a>
            <form method="POST" style="display: inline;">
                <button type="submit" name="clear_cart" class="btn btn-danger">Remove All</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>