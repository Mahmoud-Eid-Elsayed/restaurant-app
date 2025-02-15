<?php
session_start();
require_once "../../connection/db.php";
require_once "User.php";

$errors = [];
$user = new User($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $profilePic = isset($_FILES['profile_pic']) ? $_FILES['profile_pic'] : null;
    
    $result = $user->addUser($_POST, $profilePic);

    if ($result['status']) {
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['form_data'] = $_POST;
        $_SESSION['form_errors'] = $result['errors'];
        header("Location: signup.php");
        exit();
    }
}
?>
