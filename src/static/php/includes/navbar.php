<?php
// session_start(); // Ensure session starts before using session variables

$isLoggedIn = isset($_SESSION['user']);
$userProfileImage = "/src/assets/images/users/profile_pictures/default-profile.png";

if ($isLoggedIn) {
    $userProfileImage = $_SESSION['user']['profile_image'];
}

$cartItemCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        .profile-menu {
            position: relative;
            display: inline-block;
        }

        .profile-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            min-width: 150px;
        }

        .profile-dropdown a {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: black;
        }

        .profile-dropdown a:hover {
            background: #f8f9fa;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            object-fit: cover;
            /* Ensure the image fills the circle */
        }

        .icon-container {
            font-size: 24px;
            cursor: pointer;
            color: white;
            position: relative;
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
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-dark sticky-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="../../../../index.html">THE CHEF</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
                aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="offcanvas offcanvas-end bg-dark text-white" id="offcanvasNavbar">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title">Menu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav mx-auto mb-3 mb-lg-0">
                        <li class="nav-item"><a class="nav-link text-white" href="../../../../index.html">Home</a></li>
                        <li class="nav-item"><a class="nav-link text-white"
                                href="../../../static/php/menu/menu.php">Menu</a>
                        </li>
                        <li class="nav-item"><a class="nav-link text-white" href="#">Offers</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#">About Us</a></li>
                        <li class="nav-item"><a class="nav-link text-white" href="#">Contact</a></li>
                    </ul>

                    <div class="d-flex align-items-center gap-3">

                        <div class="icon-container position-relative">
                            <a href="cart.php" class="text-white"><i class="bi bi-cart"></i></a>
                            <?php if ($cartItemCount > 0): ?>
                                <span class="badge"><?= $cartItemCount ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="icon-container position-relative">
                            <a href="notifications.php" class="text-white"><i class="bi bi-bell"></i></a>
                            <span class="badge"></span>
                        </div>

                        <?php if (!$isLoggedIn): ?>
                            <a class="btn btn-outline-light btn-sm" href="../../../static/html/user/signup.php">Sign
                                Up</a>
                            <a class="btn btn-light btn-sm" href="../../../static/html/user/login.php">Log In</a>
                        <?php else: ?>
                            <div class="profile-menu">
                                <img src="<?= $userProfileImage ?>" class="profile-img" id="profileIcon">
                                <div class="profile-dropdown" id="profileDropdown">
                                    <a href="../../html/user/userProfile.php">👤 Profile</a>
                                    <a href="../../html/user/logout.php">🚪 Logout</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
        <span id="cart-count"><?= $cartItemCount ?></span>

    function updateCartCount() {
        fetch('get_cart_count.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('cart-count').innerText = data;
        });
    }
    updateCartCount();


        document.getElementById("profileIcon")?.addEventListener("click", function () {
            let dropdown = document.getElementById("profileDropdown");
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        });

        document.addEventListener("click", function (event) {
            let profileMenu = document.querySelector(".profile-menu");
            if (!profileMenu.contains(event.target)) {
                document.getElementById("profileDropdown").style.display = "none";
            }
        });
    </script>
</body>

</html>