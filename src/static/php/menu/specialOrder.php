<?php
session_start();
require '../includes/navbar.php';


$host = 'localhost';
$port = '3307';
$dbname = 'Restaurant_DB';
$username = 'root';
$password = '@Eithar1904';

$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}


$categories = [];
$categoryQuery = "SELECT CategoryID, CategoryName FROM MenuCategory";
$categoryResult = $conn->query($categoryQuery);
while ($row = $categoryResult->fetch_assoc()) {
    $categories[$row['CategoryID']] = $row['CategoryName'];
}


$stmt = $conn->prepare("
    SELECT mi.ItemID, mi.ItemName, mi.Price, mi.ImageURL, so.DiscountPercentage, so.ExpiryDate, so.OfferCode
    FROM MenuItem mi
    JOIN SpecialOffer so ON mi.ItemID = so.ItemID
    WHERE mi.Availability = 1
");
$stmt->execute();
$result = $stmt->get_result();
$specialOffers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Offers</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #ECF0F1; }
        .card { border-radius: 10px; transition: transform 0.3s, box-shadow 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2); }
        .discounted-price { text-decoration: line-through; color: red; font-size: 1rem; }
        .offer-code { background: #f1c40f; padding: 5px; border-radius: 5px; display: inline-block; }
        .countdown { font-size: 1rem; color: #e74c3c; font-weight: bold; }
        .btn-add { background-color: #27AE60; color: white; font-weight: bold; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>🔥 Special Discounts - Limited Time!</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($specialOffers as $offer): 
            $original_price = $offer['Price'];
            $discounted_price = $original_price - ($original_price * ($offer['DiscountPercentage'] / 100));
        ?>
        <div class="col">
            <div class="card">
                <img src="<?= htmlspecialchars($offer['ImageURL']) ?>" class="card-img-top" alt="<?= htmlspecialchars($offer['ItemName']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($offer['ItemName']) ?></h5>
                    <p>
                        <span class="discounted-price">$<?= number_format($original_price, 2) ?></span>
                        <strong>$<?= number_format($discounted_price, 2) ?></strong>
                    </p>
                    <?php if (!empty($offer['OfferCode'])): ?>
                        <p class="offer-code">Use Code: <strong><?= htmlspecialchars($offer['OfferCode']) ?></strong></p>
                    <?php endif; ?>
                    <p class="countdown" data-expiry="<?= $offer['ExpiryDate'] ?>"></p>
                    <button class="btn btn-add w-100 add-to-cart" data-id="<?= htmlspecialchars($offer['ItemID']) ?>">
                        🛒 Add to Cart
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// عد تنازلي لانتهاء العروض
document.querySelectorAll('.countdown').forEach(el => {
    let expiryDate = new Date(el.getAttribute('data-expiry')).getTime();
    let interval = setInterval(() => {
        let now = new Date().getTime();
        let timeLeft = expiryDate - now;
        if (timeLeft < 0) {
            el.innerHTML = "⏳ Offer Expired";
            clearInterval(interval);
            return;
        }
        let days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        let hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        el.innerHTML = `⏳ ${days}d ${hours}h ${minutes}m left`;
    }, 1000);
});

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
            
        })
        .catch(error => console.error('Error:', error));
    });
});
</script>

</body>
</html>
