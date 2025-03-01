<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';
require '../includes/navbar.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sql = "SELECT * FROM SpecialOffer";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt;

if (!$result) {
    die("Query failed");
}

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Special Offers</title>
    <style>
        body {
            background-color: #f0f0f0;
            font-family: Arial, sans-serif;
        }
        .offer-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            width: 100%;
            padding: 20px;
        }
        .offer-card {
            background: white;
            border: 1px solid #ddd;
            padding: 15px;
            width: 300px;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .offer-card img {
            max-width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        .offer-card h3 {
            color: #1f3a93;
        }
        .offer-card p {
            margin: 5px 0;
            color: #666;
        }
        .original-price {
            text-decoration: line-through;
            color: #999;
        }
        .discount-percentage {
            color: #e74c3c;
            font-weight: bold;
        }
        .final-price {
            color: #2ecc71;
            font-weight: bold;
        }
        .countdown {
            color: #1f3a93;
            font-weight: bold;
            font-size: 16px;
        }
        .add-to-cart {
            background-color: #1f3a93;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .add-to-cart:hover {
            background-color: #162c6a;
        }
    </style>
</head>
<body>
    <h2 style="width: 100%; text-align: center; color: #1f3a93;">Special Offers</h2>
    <div id="cart-status">🛒 Items in Cart: <span id="cart-count"><?= $cart_count; ?></span></div>
    <div class="offer-container">
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="offer-card">
                <?php
                $imageURL = !empty($row['ImageURL']) ? htmlspecialchars($row['ImageURL']) : 'default-image.jpg';
                ?>
                <img src="<?php echo $imageURL; ?>" alt="Offer Image">
                <h3><?php echo htmlspecialchars($row['OfferCode'] ?? ''); ?></h3>
                <p><?php echo htmlspecialchars($row['Description'] ?? ''); ?></p>

                <?php
                $originalPrice = 100;
                $discountPercentage = $row['DiscountPercentage'] ?? 0;
                $discountAmount = $row['DiscountAmount'] ?? 0;

                if ($discountPercentage > 0) {
                    $finalPrice = $originalPrice * (1 - ($discountPercentage / 100));
                } elseif ($discountAmount > 0) {
                    $finalPrice = $originalPrice - $discountAmount;
                } else {
                    $finalPrice = $originalPrice;
                }

                $finalPrice = max(0, $finalPrice);

                $currentDate = new DateTime();
                $expiryDate = clone $currentDate;
                $expiryDate->modify('+10 days');

                $timeRemaining = $expiryDate->getTimestamp() - $currentDate->getTimestamp();
                ?>

                <p><span class="original-price">$<?php echo number_format($originalPrice, 2); ?></span></p>
                <p><span class="discount-percentage"><?php echo ($discountPercentage > 0) ? $discountPercentage . '% Off' : (($discountAmount > 0) ? '$' . number_format($discountAmount, 2) . ' Off' : 'No Discount'); ?></span></p>
                <p><span class="final-price">$<?php echo number_format($finalPrice, 2); ?></span></p>
                
                <p class="countdown" data-time="<?php echo $timeRemaining; ?>">Loading...</p>
                <button class='btn btn-add w-100 add-to-cart' data-id='" . htmlspecialchars($item['ItemID']) . "">
                                    <i class='bi bi-cart-plus'></i> Add to Cart
                                </button>           
        <?php endwhile; ?>
    </div>

    <script>
        function startCountdown() {
            document.querySelectorAll('.countdown').forEach(element => {
                let timeRemaining = parseInt(element.getAttribute('data-time'));
                function updateCountdown() {
                    if (timeRemaining <= 0) {
                        element.innerHTML = "Offer Expired!";
                        return;
                    }
                    let days = Math.floor(timeRemaining / (60 * 60 * 24));
                    let hours = Math.floor((timeRemaining % (60 * 60 * 24)) / (60 * 60));
                    let minutes = Math.floor((timeRemaining % (60 * 60)) / 60);
                    let seconds = Math.floor(timeRemaining % 60);
                    element.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s Left`;
                    timeRemaining--;
                }
                updateCountdown();
                setInterval(updateCountdown, 1000);
            });
        }
        document.addEventListener('DOMContentLoaded', startCountdown);

        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
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

<?php
$conn = null;
?>
