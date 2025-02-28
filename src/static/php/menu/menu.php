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
    <title>ELCHEF - Restaurant Menu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

    <div class="menu-header text-center">
        <div class="container">
            <h1 class="display-4">Our Menu</h1>
            <p class="lead">Experience culinary excellence with our carefully crafted dishes</p>
        </div>
    </div>

    <div class="category-nav text-center">
        <div class="container">
            <a href="?category=all" class="<?= $selectedCategory === 'all' ? 'active' : '' ?>">
                <i class="fas fa-utensils"></i> All Menu
            </a>
            <?php foreach ($categories as $categoryID => $categoryName) { ?>
                <a href="?category=<?= urlencode($categoryID) ?>" 
                   class="<?= $selectedCategory == $categoryID ? 'active' : '' ?>">
                    <i class="fas fa-<?= $categoryID == 1 ? 'drumstick-bite' : 
                                    ($categoryID == 2 ? 'fish' : 
                                    ($categoryID == 3 ? 'ice-cream' : 
                                    ($categoryID == 4 ? 'glass-martini-alt' : 'utensils'))) ?>"></i>
                    <?= htmlspecialchars($categoryName) ?>
                </a>
            <?php } ?>
        </div>
    </div>

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
                $stmt->execute([$categoryID]);
                $result = $stmt;

                while ($item = $result->fetch()) {
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
                $stmt->closeCursor();
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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