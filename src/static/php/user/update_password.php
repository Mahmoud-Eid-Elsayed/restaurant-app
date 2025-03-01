<?php 
session_start();
require_once "../../connection/db.php";
require_once "User.php";

$user = new User($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = $_SESSION['email_password_reset'];

    if ($password !== $confirm_password) {
        $_SESSION['message'] = "Passwords do not match!";
        header("Location: reset_password.php");
        exit;
    }

    // Hash the password securely
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Update password in the database
    $stmt = $conn->prepare("UPDATE User SET password = ? WHERE Email = ?");
    $stmt->execute([$hashedPassword, $email]);

    $_SESSION['message'] = "Password updated successfully. You can now log in.";
    header("Location: ../../html/user/login.php");
    exit;
}
?>


