<?php
session_start();
require_once __DIR__ . '/../../connection/db.php'; // db.php provides $conn as PDO

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $product_id = $_POST["id"];

    // Fetch product details from the database using PDO
    $query = "SELECT ItemID, ItemName, Price, ImageURL FROM MenuItem WHERE ItemID = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":id", $product_id, PDO::PARAM_INT);
    $stmt->execute();
    $product = $stmt->fetch();

    if ($product) {
        // Ensure all required keys are present
        $product_data = [
            "name" => $product["ItemName"] ?? "Unknown Item", // Fallback if key is missing
            "price" => $product["Price"] ?? 0.00, // Fallback if key is missing
            "image" => $product["ImageURL"] ?? "default_image.jpg", // Fallback if key is missing
            "quantity" => 1
        ];

        // Add the product to the cart or update its quantity
        if (!array_key_exists($product_id, $_SESSION['cart'])) {
            $_SESSION['cart'][$product_id] = $product_data;
        } else {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        }
    } else {
        // Handle case where product is not found
        die("Product not found.");
    }

    // Return the updated cart count
    echo count($_SESSION['cart']);
}
?>
