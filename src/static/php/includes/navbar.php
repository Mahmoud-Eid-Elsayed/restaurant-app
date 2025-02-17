<?php
session_start(); // بدء الجلسة

// التحقق من تسجيل الدخول
$isLoggedIn = isset($_SESSION['user']); // مثال: إذا كان المستخدم مسجل دخول، سيتم تخزين بياناته في $_SESSION['user']
$userProfileImage = "src/assets/images/users/default-profile.png"; // صورة افتراضية للبروفايل

if ($isLoggedIn) {
    $userProfileImage = $_SESSION['user']['profile_image']; // جلب صورة المستخدم المسجلة
}

// عدد المنتجات في العربة (كمثال)
$cartItemCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar with Profile, Cart & Notification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        /* تحسين شكل الأيقونات */
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
        <a class="navbar-brand text-white" href="#">MySite</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar"
            aria-controls="offcanvasNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end bg-dark text-white" id="offcanvasNavbar">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">Menu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav mx-auto mb-3 mb-lg-0">
                    <li class="nav-item"><a class="nav-link text-white" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Menu</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Offers</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">About Us</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="#">Contact</a></li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <!-- أيقونة عربة التسوق -->
                    <div class="icon-container position-relative">
                        <a href="cart.php" class="text-white"><i class="bi bi-cart"></i></a>
                        <?php if ($cartItemCount > 0) : ?>
                            <span class="badge"><?= $cartItemCount ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- أيقونة الإشعارات -->
                    <div class="icon-container position-relative">
                        <a href="notifications.php" class="text-white"><i class="bi bi-bell"></i></a>
                        <span class="badge">3</span> <!-- مثال: عدد الإشعارات -->
                    </div>

                    <?php if (!$isLoggedIn) : ?>
                        <button class="btn btn-outline-light btn-sm">Sign Up</button>
                        <button class="btn btn-light btn-sm" h>Log In</button>
                    <?php else : ?>
                        <!-- صورة البروفايل وقائمة منسدلة -->
                        <div class="profile-menu">
                            <img src="<?= $userProfileImage ?>" class="profile-img" id="profileIcon">
                            <div class="profile-dropdown" id="profileDropdown">
                                <a href="profile.php">👤 Profile</a>
                                <a href="logout.php">🚪 Logout</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</nav>

<script>
    // إظهار القائمة عند الضغط على الصورة
    document.getElementById("profileIcon")?.addEventListener("click", function () {
        let dropdown = document.getElementById("profileDropdown");
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
    });

    // إخفاء القائمة عند الضغط خارجها
    document.addEventListener("click", function (event) {
        let profileMenu = document.querySelector(".profile-menu");
        if (!profileMenu.contains(event.target)) {
            document.getElementById("profileDropdown").style.display = "none";
        }
    });
</script>

</body>
</html>
