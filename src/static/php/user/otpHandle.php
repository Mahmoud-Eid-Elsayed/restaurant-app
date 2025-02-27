<?php
require_once "../../connection/db.php";
require_once "User.php";
$user = new User($conn);
$id=$_SESSION['id'];

$result=$user->verifyOTP($id);

?>