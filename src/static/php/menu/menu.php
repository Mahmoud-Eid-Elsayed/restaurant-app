<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


$servername = "localhost";
$username = "root";
$password = "@Eithar1904";
$dbname = "Restaurant_DB";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$requiredCategories = [1 => 'Smoothies', 2 => 'Juices'];
foreach ($requiredCategories as $id => $name) {
    $checkCategoryQuery = "SELECT * FROM MenuCategory WHERE CategoryID = $id";
    $result = $conn->query($checkCategoryQuery);
    if ($result->num_rows == 0) {
        $conn->query("INSERT INTO MenuCategory (CategoryID, CategoryName) VALUES ($id, '$name')");
    }
}


$newItems = [
    ["Checo Blast", "Delicious chocolate smoothie", 12, "chocolate-smoothie_1339-2856.jpg", 1, 1],
    ["Berry Smoothies", "Refreshing berry mix", 7, "berry-smoothies_74190-1476.jpg", 2, 1]
];


foreach ($newItems as $item) {
    $itemName = $item[0];
    $categoryID = $item[4];

    
    $checkQuery = "SELECT * FROM MenuItem WHERE ItemName = '$itemName'";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult->num_rows == 0) {
    
        $sqlInsert = "INSERT INTO MenuItem (ItemName, Description, Price, ImageURL, CategoryID, Availability) 
                      VALUES ('$item[0]', '$item[1]', $item[2], '$item[3]', $item[4], $item[5])";
        
        if ($conn->query($sqlInsert) === TRUE) {
            echo "✅ Item '{$item[0]}' added successfully.<br>";
        } else {
            echo "❌ Error adding item '{$item[0]}': " . $conn->error . "<br>";
        }
    } else {
        echo "⚠️ Item '{$item[0]}' already exists.<br>";
    }
}


$categoryMap = [];
$categoryQuery = "SELECT CategoryID, CategoryName FROM MenuCategory";
$categoryResult = $conn->query($categoryQuery);
while ($row = $categoryResult->fetch_assoc()) {
    $categoryMap[$row['CategoryID']] = $row['CategoryName'];
}


$sql = "SELECT ItemID, ItemName, Description, Price, ImageURL, CategoryID FROM MenuItem WHERE Availability = 1";
$result = $conn->query($sql);

$menuItems = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = $row;
    }
} else {
    echo "<p>No items available.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel="stylesheet" href="../src/assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="test.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm btn-success">
     
    </nav>

    <div class="container-fluid w-75">
        <?php
        foreach ($categoryMap as $id => $category) {
            echo "<div class='col mt-3 mb-3'><h1 id='$id'>$category</h1></div>";
            echo "<div class='row row-cols-2 row-cols-md-4 g-4 mx-3'>";

            foreach ($menuItems as $item) {
                if ($item['CategoryID'] == $id) {
                    echo "
                    <div class='col'>
                        <div class='card'>
                            <img src='{$item['ImageURL']}' class='card-img-top' alt='{$item['ItemName']}'>
                            <div class='card-body'>
                                <h3 class='card-title fs-3'>{$item['ItemName']}</h3>
                                <h3 class='card-title fs-4'>Price <p class='text-dark fs-4'>{$item['Price']}$</p></h3>
                                <button class='btn btn-success w-100'>
                                    <i class='bi bi-cart-plus'></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>";
                }
            }
            echo "</div>";
        }
        ?>
    </div>

    <script src="../src/assets/libraries/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script>
    document.querySelectorAll('.btn-success').forEach(button => {
        button.addEventListener('click', () => {
            const itemName = button.closest('.card').querySelector('.card-title').innerText;
            alert(`Added ${itemName} to cart!`);
        });
    });
    </script>
</body>

</html>