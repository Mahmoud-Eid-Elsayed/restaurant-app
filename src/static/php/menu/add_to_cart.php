<?php
session_start();
require_once __DIR__ . '/../../connection/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $itemID = $_POST['id'];
    
    try {
        $stmt = $conn->prepare("SELECT ItemName, Price, ImageURL FROM MenuItem WHERE ItemID = ? AND Availability = 1");
        $stmt->execute([$itemID]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($item) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            if (isset($_SESSION['cart'][$itemID])) {
                $_SESSION['cart'][$itemID]['quantity']++;
            } else {
                $_SESSION['cart'][$itemID] = [
                    'name' => $item['ItemName'],
                    'price' => $item['Price'],
                    'image' => !empty($item['ImageURL']) ? $item['ImageURL'] : 'path/to/default/image.jpg',
                    'quantity' => 1
                ];
            }
        }
        
        $_SESSION['cart_count'] = array_sum(array_column($_SESSION['cart'], 'quantity'));

        echo $_SESSION['cart_count']; 
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request";
}
