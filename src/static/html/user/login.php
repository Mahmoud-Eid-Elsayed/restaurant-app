<?php
session_start();
require '../../connection/db.php';


$formData = $_SESSION['form_data'] ?? [];
$formErrors = $_SESSION['form_errors'] ?? [];

if (isset($_SESSION['form_errors'])) {
    unset($_SESSION['form_errors']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../assets/libraries/fontawesome-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/login&register/login.css">
    <title>Login</title>

</head>

<body>
    <div class="container form-container">
        <div class="avatar-container mx-auto">
            <img src="../../../assets/images/login&register/chef-cash-register_18591-35958.avif" alt="Avatar">
        </div>
        <div class="form-wrapper">
            <form method="post" action="../../php/user/loginHandle.php">
                <div class="form-group my-3">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" placeholder="Email"
                        value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                    <div class="error"> <?= $formErrors['email'] ?? '' ?> </div>
                </div>
                <div class="form-group my-3">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                    <div class="error"> <?= $formErrors['password'] ?? '' ?> </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block my-3">Login</button>
                <div class="text-center">
                    <a href="signup.php">Don't have an account? Register</a>
                </div>
                <div class="text-center">
                    <a href="forgot_password.php">Forget your password?</a>
                </div>

            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>
</body>

</html>