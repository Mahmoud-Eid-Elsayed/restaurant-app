<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin: 100px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-control {
            border-radius: 5px;
        }
        .btn-primary {
            width: 100%;
        }
        .toggle-password {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 38px;
            font-size: 18px;
            color: #777;
        }
        .toggle-password:hover {
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h3 class="text-center">Reset Password</h3>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-danger text-center">
            <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form action="update_password.php" method="post">
        <div class="mb-3 position-relative">
            <label for="password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <span class="toggle-password" onclick="togglePassword('password')">👁</span>
        </div>

        <div class="mb-3 position-relative">
            <label for="confirm_password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            <span class="toggle-password" onclick="togglePassword('confirm_password')">👁</span>
        </div>

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>

<!-- JavaScript for Password Toggle -->
<script>
    function togglePassword(fieldId) {
        var field = document.getElementById(fieldId);
        field.type = (field.type === "password") ? "text" : "password";
    }
</script>

</body>
</html>
