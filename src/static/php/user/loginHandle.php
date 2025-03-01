<?php
require_once __DIR__ . '/../../connection/db.php';
require_once 'User.php';
session_start();

$user = new User($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $_SESSION['form_data'] = ['email' => $email];
    $_SESSION['form_errors'] = [];

    // Validate inputs
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

    // Attempt to log in the user
    $result = $user->loginUser($email, $password);

    if ($result['status']) {
        // Set session variables
        $_SESSION['user'] = [
            'id' => $result['user_id'],
            'username' => $result['username'],
            'role' => $result['role'],
            'profile_image' => $result['profile_image'],
            'status'=>$result['status'],
        ];

        // Redirect based on role
        if ($result['role'] === 'Staff') {
            header("Location: ../../../../admin-dashboard/index.php");
        } else if ($result['user']['status'] == 'inactive') { 
            header("Location: ../../php/user/otp.php");
        } else {
            header("Location: ../../html/user/userProfile.php");
        }
        exit();
    } else {
        // Login failed: Set error message
        $_SESSION['form_errors']['email'] = $result['message'];
        header("Location: ./../../html/user/login.php");
        exit();
    }
}

// Redirect to login page if the request method is not POST
header("Location: login.php");
exit();
?>