<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']); 
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'], $_POST['action'])) {
    $id = $_POST['id'];

    if ($_POST['action'] == 'increase') {
        $_SESSION['cart'][$id]['quantity']++;
    } elseif ($_POST['action'] == 'decrease' && $_SESSION['cart'][$id]['quantity'] > 1) {
        $_SESSION['cart'][$id]['quantity']--;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_id'])) {
    $remove_id = $_POST['remove_id'];
    unset($_SESSION['cart'][$remove_id]);
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
</head>
<body>
<?php require '../includes/navbar.php'; ?>


<div class="icon-container position-relative">
    <a href="cart.php" class="text-white"><i class="bi bi-cart"></i></a>
    <?php if ($cart_count > 0): ?>
        <span class="badge bg-danger"><?= $cart_count ?></span>
    <?php endif; ?>
</div>

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
                            <button type="submit" name="action" value="decrease" class="btn btn-sm btn-danger">-</button>
                            <span class="mx-2"><?php echo $item['quantity']; ?></span>
                            <button type="submit" name="action" value="increase" class="btn btn-sm btn-success">+</button>
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
