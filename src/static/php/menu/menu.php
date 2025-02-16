<<<<<<< Updated upstream
=======

>>>>>>> Stashed changes
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Menu</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
<<<<<<< Updated upstream
        /* الألوان والتصميم الجديد */
        body {
            background-color: #ECF0F1;
        }

        .navbar {
            background-color: #2C3E50;
            padding: 15px;
            border-radius: 10px;
        }

        .navbar a {
            color: white;
            font-weight: bold;
            margin-right: 15px;
            text-decoration: none; /* إزالة التسطير */
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #E74C3C;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            padding: 1rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2C3E50;
        }

        .price {
            font-size: 1.3rem;
            color: #E74C3C;
            font-weight: bold;
        }

        .btn-add {
            background-color: #27AE60;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            transition: background 0.3s;
        }

        .btn-add:hover {
            background-color: #219150;
        }
        i{
            color:black;
        }
    </style>
</head>
<body>
<?php require '../includes/navbar.php'; ?>

<img src="./test/menu offer.png" class="img-fluid w-100 " style="height: 400px;"  alt="...">



        <!-- شريط التنقل -->
        <section class="navbar navbar-expand-lg sticky-top shadow-sm">
            <div class="container">
                <a class="navbar-brand text-white" href="?category=all">🍽 Restaurant Menu</a>
                <a href="?category=Breakfast">🍳 Breakfast</a>
                <a href="?category=Main Dish">🥘 Main Dish</a>
                <a href="?category=Drink">🥤 Drink</a> 
            </div>
        </section>

        <!-- عرض القائمة -->
        <div class="container mt-5">
            <?php
            // الاتصال بقاعدة البيانات
            $conn = new mysqli("localhost", "root", "@Eithar1904", "Restaurant_DB");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // تحديد الفئة المطلوبة من الرابط
            $selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

            // جلب الفئات
            $categories = [];
            $categoryQuery = "SELECT CategoryID, CategoryName FROM MenuCategory";
            $categoryResult = $conn->query($categoryQuery);
            while ($row = $categoryResult->fetch_assoc()) {
                $categories[$row['CategoryID']] = $row['CategoryName'];
            }

            echo "<div class='row row-cols-1 row-cols-md-4 g-4'>"; // عرض كل العناصر بشكل متجاور

            // عرض العناصر بناءً على الفئة المختارة
            foreach ($categories as $categoryID => $categoryName) {
                if ($selectedCategory !== 'all' && $selectedCategory !== $categoryName) {
                    // دمج Smoothies و Juices مع Drink
                    if ($selectedCategory === 'Drink' && ($categoryName === 'Smoothies' || $categoryName === 'Juices')) {
                        // السماح بعرض العناصر بدون تخطي الكود
                    } else {
                        continue; // تخطي الفئات غير المطلوبة
                    }
                }

                // جلب العناصر المتاحة لهذه الفئة
=======
       
        body {
            background-color: #ECF0F1;
        }

        .navbar {
            background-color: #2C3E50;
            padding: 15px;
            border-radius: 10px;
        }

        .navbar a {
            color: white;
            font-weight: bold;
            margin-right: 15px;
            text-decoration: none; 
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #E74C3C;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            background: white;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            height: 220px;
            object-fit: cover;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            padding: 1rem;
            text-align: center;
        }

        .card-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2C3E50;
        }

        .price {
            font-size: 1.3rem;
            color: #E74C3C;
            font-weight: bold;
        }

        .btn-add {
            background-color: #27AE60;
            color: white;
            font-weight: bold;
            border-radius: 10px;
            transition: background 0.3s;
        }

        .btn-add:hover {
            background-color: #219150;
        }
        i{
            color:black;
        }
    </style>
</head>
<body>
<?php require '../includes/navbar.php'; ?>

<img src="./test/menu offer.png" class="img-fluid w-100 " style="height: 400px;"  alt="...">


        <section class="navbar navbar-expand-lg sticky-top shadow-sm">
            <div class="container">
                <a class="navbar-brand text-white" href="?category=all">🍽 Restaurant Menu</a>
                <a href="?category=Breakfast">🍳 Breakfast</a>
                <a href="?category=Main Dish">🥘 Main Dish</a>
                <a href="?category=Drink">🥤 Drink</a> 
            </div>
        </section>

       
        <div class="container mt-5">
            <?php
            
            $conn = new mysqli("localhost", "root", "@Eithar1904", "Restaurant_DB");
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'all';

          
            $categories = [];
            $categoryQuery = "SELECT CategoryID, CategoryName FROM MenuCategory";
            $categoryResult = $conn->query($categoryQuery);
            while ($row = $categoryResult->fetch_assoc()) {
                $categories[$row['CategoryID']] = $row['CategoryName'];
            }

            echo "<div class='row row-cols-1 row-cols-md-4 g-4'>"; 

            foreach ($categories as $categoryID => $categoryName) {
                if ($selectedCategory !== 'all' && $selectedCategory !== $categoryName) {
                  
                    if ($selectedCategory === 'Drink' && ($categoryName === 'Smoothies' || $categoryName === 'Juices')) {
                      
                    } else {
                        continue;
                    }
                }

>>>>>>> Stashed changes
                $itemQuery = "SELECT ItemName, Price, ImageURL FROM MenuItem WHERE CategoryID = $categoryID AND Availability = 1";
                $itemResult = $conn->query($itemQuery);

                if ($itemResult->num_rows > 0) {
                    while ($item = $itemResult->fetch_assoc()) {
                        echo "
                            <div class='col'>
                                <div class='card'>
                                    <img src='{$item['ImageURL']}' class='card-img-top' alt='{$item['ItemName']}'>
                                    <div class='card-body'>
                                        <h5 class='card-title'>{$item['ItemName']}</h5>
                                        <p class='price'>\${$item['Price']}</p>
                                        <button class='btn btn-add w-100'>
                                            <i class='bi bi-cart-plus ' ></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>";
                    }
                }
            }

<<<<<<< Updated upstream
            echo "</div>"; // إغلاق الـ row

            // إغلاق الاتصال بقاعدة البيانات
=======
            echo "</div>";

        
>>>>>>> Stashed changes
            $conn->close();
            ?>
        </div>
   
</body>
</html>
