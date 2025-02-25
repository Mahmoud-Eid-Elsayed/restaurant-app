<?php
session_start();
require '../includes/navbar.php';
require_once __DIR__ . '/../../connection/db.php';

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$selectedCategory = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'all';

$categories = [];
try {
    $categoryQuery = "SELECT CategoryID, CategoryName FROM MenuCategory";
    $categoryResult = $conn->query($categoryQuery);

    while ($row = $categoryResult->fetch(PDO::FETCH_ASSOC)) {
        $categories[$row['CategoryID']] = $row['CategoryName'];
    }
} catch (PDOException $e) {
    die("Error fetching categories: " . $e->getMessage());
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
        .menu-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }
        
        .menu-item {
            width: calc(50% - 10px);
            text-align: center;
            background-color: #333;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }
        
        .menu-item img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        
        .menu-item h5, .menu-item p {
            margin-top: 10px;
            color: orange;
        }
        
        .btn-add {
            background-color: orange;
            color: black;
            font-weight: bold;
            border-radius: 10px;
            transition: background 0.3s;
            border: none;
            padding: 10px 15px;
            width: 100%;
            margin-top: 10px;
        }
        
        .btn-add:hover {
            background-color: darkorange;
        }

        .navbar1 {
            background-color: black;
            padding: 10px;
        }

        .navbar1 a {
            color: orange;
            text-decoration: none;
            margin-right: 15px;
        }

        .navbar1 a:hover {
            color: darkorange;
        }

        .container h2 {
            color: orange;
            text-align: center;
            margin-bottom: 20px;
        }

        #cart-status {
            color: black;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
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
        <div class="menu-container">
            <?php 
            foreach ($categories as $categoryID => $categoryName) {
                if ($selectedCategory !== 'all' && $selectedCategory != $categoryID) {
                    continue;
                }

                try {
                    $stmt = $conn->prepare("SELECT ItemID, ItemName, Price, ImageURL FROM MenuItem WHERE CategoryID = ? AND Availability = 1");
                    $stmt->execute([$categoryID]);
                    $result = $stmt;

                    if ($result->rowCount() == 0) {
                        echo "<p style='color: red;'>⚠ No items found in this category.</p>";
                    }

                    while ($item = $result->fetch(PDO::FETCH_ASSOC)) {
                        $imageURL = !empty($item['ImageURL']) ? htmlspecialchars($item['ImageURL']) : 'path/to/default/image.jpg';
                        echo "
                        <div class='menu-item'>
                            <img src='$imageURL' alt='" . htmlspecialchars($item['ItemName']) . "'>
                            <h5>" . htmlspecialchars($item['ItemName']) . "</h5>
                            <p>\$" . htmlspecialchars($item['Price']) . "</p>
                            <button class='btn btn-add add-to-cart' data-id='" . htmlspecialchars($item['ItemID']) . "'>
                                <i class='bi bi-cart-plus'></i> Add to Cart
                            </button>
                        </div>";
                    }
                    $stmt->closeCursor();
                } catch (PDOException $e) {
                    die("Error fetching items: " . $e->getMessage());
                }
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
                alert('Item added to cart!');
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>
</body>
</html>