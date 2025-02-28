<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../connection/db.php';
require '../includes/navbar.php';

// Database connection
$conn = new mysqli($host, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Fetch active special offers
$stmt = $conn->prepare("
    SELECT OfferID, OfferName, Description, EndDate, ImageURL
    FROM specialoffer
    WHERE IsActive = 1
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
        body { background-color: #f8f9fa; font-family: Arial, sans-serif; }
        .card { border-radius: 12px; transition: 0.3s; box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1); }
        .card:hover { transform: translateY(-5px); box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.2); }
        .countdown { font-size: 1rem; color: #dc3545; font-weight: bold; }
        .discount { font-size: 1.2rem; color: #e67e22; font-weight: bold; }
        .add-to-cart-btn { background-color: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; }
        .add-to-cart-btn:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">🔥 Special Discounts - Limited Time!</h2>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($specialOffers as $offer): 
            $imageURL = isset($offer['ImageURL']) ? trim($offer['ImageURL']) : 'default.jpg';
            $offerID = isset($offer['OfferID']) ? intval($offer['OfferID']) : 0;
            $discount = 20; // Fixed discount at 20%
        ?>
        <div class="col">
            <div class="card p-3">
                <img src="<?= htmlspecialchars($imageURL) ?>" class="card-img-top" alt="<?= htmlspecialchars($offer['OfferName']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($offer['OfferName']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($offer['Description']) ?></p>
                    <p class="discount">Discount: <?= $discount ?>%</p>
                    <p class="countdown" data-expiry="<?= htmlspecialchars($offer['EndDate']) ?>"></p>
                    <a href="src/static/php/menu/cart.php?offer_id=<?= $offerID ?>" class="add-to-cart-btn">Add to Cart</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
// Countdown timer with reset on expiry
document.querySelectorAll('.countdown').forEach(el => {
    let expiryDate = new Date(el.getAttribute('data-expiry')).getTime();
    let interval = setInterval(() => {
        let now = new Date().getTime();
        let timeLeft = expiryDate - now;
        if (timeLeft < 0) {
            el.innerHTML = "⏳ Offer Expired - Restarting...";
            clearInterval(interval);
            setTimeout(() => location.reload(), 3000); // Reload page after 3 seconds
            return;
        }
        let days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        let hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        el.innerHTML = `⏳ ${days}d ${hours}h ${minutes}m left`;
    }, 1000);
});
</script>

</body>
</html>
