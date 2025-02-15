<?php
require 'db.php';
require_once 'User.php';
session_start();

$user = new User($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $_SESSION['form_data'] = ['email' => $email];
    $_SESSION['form_errors'] = [];

    if (empty($email)) {
        $_SESSION['form_errors']['email'] = "Email is required.";
    }
    if (empty($password)) {
        $_SESSION['form_errors']['password'] = "Password is required.";
    }

    if (!empty($_SESSION['form_errors'])) {
        header("Location: login.php");
        exit();
    }

    $result = $user->loginUser($email, $password);

    if ($result['status']) {
        if ($result['role'] === 'Staff') {
            header("Location: dashboard.php");
        } else {
            header("Location: home.php");
        }
        exit();
    } else {
        $_SESSION['form_errors']['email'] = $result['message'];
        header("Location: login.php");
        exit();
    }
}
header("Location: login.php");
exit();
?>
