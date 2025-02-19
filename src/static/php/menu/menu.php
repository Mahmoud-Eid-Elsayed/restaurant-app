<?php

session_start();
require '../includes/navbar.php';

$host = 'localhost';
$port = '3307'; 
$dbname = 'Restaurant_DB';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

$categories = [];
$categoryQuery = "SELECT CategoryID, CategoryName FROM MenuCategory";
$categoryResult = $conn->query($categoryQuery);

while ($row = $categoryResult->fetch_assoc()) {
    $categories[$row['CategoryID']] = $row['CategoryName'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #ECF0F1; }
        .navbar { background-color: #2C3E50;  padding: 15px; border-radius: 10px; }
        .navbar1 { background-color: #2C3E50;  padding: 15px; border-radius: 10px;
            z-index: 1000;
            text-align: center; 
    position: sticky;
    top: 80px; }

        .navbar1 a { color: white; font-weight: bold; margin-right: 35px; text-decoration: none; transition: color 0.3s; }
        .navbar a:hover { color: #E74C3C; }
        .card { border: none; border-radius: 15px; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; background: white; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); }
        .card:hover { transform: translateY(-5px); box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2); }
        .card-img-top { height: 220px; object-fit: cover; }
        .price { font-size: 1.3rem; color: #E74C3C; font-weight: bold; }
        .btn-add { background-color: #27AE60; color: white; font-weight: bold; border-radius: 10px; transition: background 0.3s; }
        .btn-add:hover { background-color: #219150; }
    </style>
</head>
<body>
<img src="" alt="">
<section class="navbar1 navbar-expand-lg py-3 position-sticky d-flex">
    <div class="container">
        <a class="navbar-brand text-white" href="?category=all">🍽 Restaurant Menu</a>
        <?php foreach ($categories as $categoryID => $categoryName) { ?>
            <a href="?category=<?= urlencode($categoryID) ?>">🍛 <?= htmlspecialchars($categoryName) ?></a>
        <?php } ?>
    </div>
</section>

<div class="container mt-5">
    <h2>Restaurant Menu</h2>
    <div id="cart-status">🛒 Items in Cart: <span id="cart-count"><?= $cart_count; ?></span></div>
    <div class="row row-cols-1 row-cols-md-4 g-4">
        <?php 
        foreach ($categories as $categoryID => $categoryName) {
            if ($selectedCategory !== 'all' && $selectedCategory != $categoryID) {
                continue;
            }

            $stmt = $conn->prepare("SELECT ItemID, ItemName, Price, ImageURL FROM MenuItem WHERE CategoryID = ? AND Availability = 1");
            $stmt->bind_param("i", $categoryID);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($item = $result->fetch_assoc()) {
                echo "
                    <div class='col'>
                        <div class='card'>
                            <img src='" . htmlspecialchars($item['ImageURL']) . "' class='card-img-top' alt='" . htmlspecialchars($item['ItemName']) . "'>
                            <div class='card-body'>
                                <h5 class='card-title'>" . htmlspecialchars($item['ItemName']) . "</h5>
                                <p class='price'>\$" . htmlspecialchars($item['Price']) . "</p>
                                <button class='btn btn-add w-100 add-to-cart' data-id='" . htmlspecialchars($item['ItemID']) . "'>
                                    <i class='bi bi-cart-plus'></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>";
            }
            $stmt->close();
        }
        ?>
    </div>
</div>

<script>
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        let itemID = this.getAttribute('data-id');
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${itemID}`
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('cart-count').innerText = data;
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

</body>
</html>
