<?php
require_once __DIR__ . '/../../connection/db.php';

try {
    $stmt = $conn->query("SELECT ItemID, ItemName, Price, ImageURL, CategoryID FROM MenuItem");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Menu Items in Database:\n\n";
    foreach ($items as $item) {
        echo "ID: " . $item['ItemID'] . "\n";
        echo "Name: " . $item['ItemName'] . "\n";
        echo "Price: $" . $item['Price'] . "\n";
        echo "Category: " . $item['CategoryID'] . "\n";
        echo "Image URL: " . $item['ImageURL'] . "\n";
        echo "-------------------\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 