<?php
$host = 'localhost';
$port = '3307'; 
$dbname = 'Restaurant_DB';
$username = 'root';
$password = '';




try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>