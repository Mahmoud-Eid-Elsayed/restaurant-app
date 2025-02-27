<?php
require_once "../../connection/db.php";
require_once "User.php";
$user = new User($conn);
$email=$_SESSION['email'];

$result=$user->generateOTP($email);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .otp-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .otp-input {
            width: 200px;
            height: 40px;
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
            border: 2px solid #ccc;
            border-radius: 5px;
        }
        .otp-input:focus {
            border-color: #007bff;
            outline: none;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .btn:disabled {
            background: #ccc;
        }
        .resend {
            margin-top: 10px;
            color: #007bff;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <h2>Enter OTP</h2>
        <p>We have sent a verification code to your email.</p>
        <form action="otpHandle.php" method="POST">
            <input type="text" class="otp-input" maxlength="6" name="otp" placeholder="Enter OTP" required>
            <br>
            <button class="btn" type="submit">Verify</button>
        </form>
        <form action="resend_otp.php" method="POST">
            <button class="btn" type="submit">Resend OTP</button>
        </form>
    </div>
</body>
</html>
