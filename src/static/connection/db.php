<?php
$host = 'localhost';
$port = '3307';
$dbname = 'restaurant_db';
$username = 'root';
$password = '@Eithar1904';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8'");

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>