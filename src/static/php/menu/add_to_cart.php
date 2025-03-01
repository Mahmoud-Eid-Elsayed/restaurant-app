<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $itemId = $_POST['id'];
    
    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if item exists in database and get all necessary details
    $stmt = $conn->prepare("SELECT ItemID, ItemName, Price, ImageURL FROM MenuItem WHERE ItemID = ? AND Availability = 1");
    $stmt->execute([$itemId]);
    $item = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($item) {
        // Add item to cart or increment quantity if already exists
        if (isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId]['quantity']++;
        } else {
            $_SESSION['cart'][$itemId] = [
                'id' => $item['ItemID'],
                'name' => $item['ItemName'],
                'price' => $item['Price'],
                'image' => $item['ImageURL'],
                'quantity' => 1
            ];
        }
        
        // Calculate total number of items in cart
        $total_items = array_sum(array_map(function($item) {
            return $item['quantity'];
        }, $_SESSION['cart']));
        
        echo $total_items;
    } else {
        http_response_code(404);
        echo "Item not found or not available";
    }
} else {
    http_response_code(400);
    echo "Invalid request";
}
?>
