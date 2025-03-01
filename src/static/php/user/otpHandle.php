<?php
session_start();
require_once "../../connection/db.php";
require_once "User.php";
$user = new User($conn);
$id=$_SESSION['user_id'];
$input=$_POST['otp'];
$result=$user->verifyOTP($input);
if(isset($_SESSION['setpassword'])){
    
}
?>