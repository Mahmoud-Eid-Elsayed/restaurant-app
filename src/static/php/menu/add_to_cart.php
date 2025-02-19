<?php
session_start();
$conn = new mysqli("localhost", "root", "@Eithar1904", "Restaurant_DB");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $product_id = $_POST["id"];

  
    $query = "SELECT ItemName, Price, ImageURL FROM MenuItem WHERE ItemID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
     
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (!array_key_exists($product_id, $_SESSION['cart'])) {
            $_SESSION['cart'][$product_id] = [
                "name" => $product["ItemName"],
                "price" => $product["Price"],
                "image" => $product["ImageURL"],
                "quantity" => 1
            ];
        } else {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        }
    }

  
    echo count($_SESSION['cart']);
}
?>
