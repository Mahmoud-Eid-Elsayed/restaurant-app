<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

// Fetch categories
$categories = [];
$categoryQuery = "SELECT CategoryID, CategoryName FROM MenuCategory";
$categoryResult = $conn->query($categoryQuery);

while ($row = $categoryResult->fetch(PDO::FETCH_ASSOC)) {
    $categories[$row['CategoryID']] = $row['CategoryName'];
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
    <link rel="stylesheet" href="../../../css/landingpage/landing-pg.css">
    <style>
        :root {
            --primary-color: #e67e22;
            --secondary-color: #2c3e50;
            --accent-color: #f39c12;
            --text-dark: #2d3436;
            --text-light: #636e72;
            --white: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: var(--text-dark);
            line-height: 1.6;
        }

        .menu-header {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #1a252f 100%);
            padding: 4rem 0 6rem;
            margin-bottom: -4rem;
            position: relative;
            overflow: hidden;
        }

        .menu-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../../../assets/images/pattern.png');
            opacity: 0.1;
        }

        .menu-header h1 {
            color: var(--white);
            font-weight: 700;
            margin-bottom: 1rem;
            position: relative;
            animation: fadeInUp 0.6s ease-out;
        }

        .menu-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        .category-nav {
            background-color: var(--white);
            padding: 1rem 0;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 80px;
            z-index: 1000;
            margin: 0 auto 2rem;
            max-width: 90%;
            animation: slideInBottom 0.6s ease-out;
        }

        .category-nav a {
            color: var(--text-dark);
            text-decoration: none;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            margin: 0.5rem;
        }

        .category-nav a:hover,
        .category-nav a.active {
            background-color: var(--primary-color);
            color: var(--white);
            transform: translateY(-2px);
        }

        .category-nav a i {
            margin-right: 8px;
            font-size: 1.1rem;
        }

        .menu-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            background: var(--white);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            height: 100%;
            opacity: 0;
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        }

        .menu-card img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .menu-card:hover img {
            transform: scale(1.1);
        }

        .menu-card .card-body {
            padding: 1.5rem;
        }

        .menu-card .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .menu-card .price {
            font-size: 1.25rem;
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .btn-add-cart {
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-add-cart:hover {
            background-color: #d35400;
            transform: translateY(-2px);
        }

        /* Navbar Cart Styles */
        .nav-cart {
            position: relative;
            display: flex;
            align-items: center;
            margin-left: 15px;
        }

        .nav-cart-link {
            color: var(--text-dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .nav-cart-link:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .nav-cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--primary-color);
            color: var(--white);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 600;
            border: 2px solid var(--white);
        }

        @media (max-width: 768px) {
            .category-nav {
                overflow-x: auto;
                white-space: nowrap;
                padding: 0.5rem;
            }

            .menu-header {
                padding: 3rem 0 5rem;
            }

            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 1.5rem;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInBottom {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .menu-section {
            padding: 2rem 0;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        /* Add success notification */
        .notification {
            position: fixed;
            top: 25%;
            right: 20px;
            padding: 15px 25px;
            background-color: #2ecc71;
            color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
            z-index: 100000;
            transform: translateY(-100px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }

        .notification i {
            font-size: 1.2rem;
        }
    </style>
</head>

<body>
    <!-- Include navbar only once -->
    <?php include '../includes/navbar.php'; ?>

    <!-- Add notification element -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle"></i>
        <span>Item added to cart!</span>
    </div>

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

    <section class="menu-section">
        <div class="container">
            <div class="menu-grid">
                <?php
                foreach ($categories as $categoryID => $categoryName) {
                    if ($selectedCategory !== 'all' && $selectedCategory != $categoryID) {
                        continue;
                    }

                    $stmt = $conn->prepare("SELECT ItemID, ItemName, Price, ImageURL FROM MenuItem WHERE CategoryID = ? AND Availability = 1");
                    $stmt->execute([$categoryID]);
                    $result = $stmt;

                    while ($item = $result->fetch()) {
                        // Fix the image URL path
                        $imageUrl = '../../../static/uploads/Menu-item/' . basename($item['ImageURL']);

                        echo "
                        <div class='menu-card'>
                            <div class='card-img-wrapper'>
                                <img src='" . htmlspecialchars($imageUrl) . "' 
                                     class='card-img-top' 
                                     alt='" . htmlspecialchars($item['ItemName']) . "'>
                            </div>
                            <div class='card-body'>
                                <h5 class='card-title'>" . htmlspecialchars($item['ItemName']) . "</h5>
                                <div class='price'>$" . number_format($item['Price'], 2) . "</div>
                                <button class='btn-add-cart add-to-cart' data-id='" . htmlspecialchars($item['ItemID']) . "'>
                                    <i class='fas fa-cart-plus'></i>
                                    Add to Cart
                                </button>
                            </div>
                        </div>";
                    }
                    $stmt->closeCursor();
                }
                ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add notification function
        function showNotification() {
            const notification = document.getElementById('notification');
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        function updateCartCount(count) {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.style.transform = 'scale(1.2)';
                cartCount.textContent = count;
                setTimeout(() => {
                    cartCount.style.transform = 'scale(1)';
                }, 200);
            }
        }

        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function () {
                const btn = this;
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

                let itemID = this.getAttribute('data-id');
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${itemID}`
                })
                    .then(response => response.text())
                    .then(data => {
                        updateCartCount(data);
                        btn.innerHTML = '<i class="fas fa-check"></i> Added!';
                        showNotification();

                        setTimeout(() => {
                            btn.disabled = false;
                            btn.innerHTML = '<i class="fas fa-cart-plus"></i> Add to Cart';
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-cart-plus"></i> Add to Cart';
                    });
            });
        });

        // Animate menu cards on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationDelay = `${entry.target.dataset.delay}s`;
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.menu-card').forEach((card, index) => {
            card.style.animationPlayState = 'paused';
            card.dataset.delay = index * 0.1;
            observer.observe(card);
        });
    </script>
</body>

</html>