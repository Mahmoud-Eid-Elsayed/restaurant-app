<?php
require_once "User.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = new User($pdo);
$userData = $user->getUserById($_SESSION['user_id']);

if (!$userData) {
    die("User not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../../assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #e3f2fd;
            color: #333;
            box-shadow: 2px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: #333;
            font-weight: bold;
            padding: 10px 15px;
            transition: 0.3s;
        }
        .sidebar .nav-link:hover {
            background: #bbdefb;
            border-radius: 5px;
        }
        .profile-container {
            margin-top: 20px;
            text-align: center;
        }
        .profile-container img {
            border: 3px solid #90caf9;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="d-flex">
<?php include 'sidebar.php'; ?>

    <!-- محتوى البروفايل -->
    <div class="container mt-3">
        <div class="profile-container">
            <img src="<?= htmlspecialchars($user['ProfilePictureURL'] ?? 'default.png') ?>" width="150" height="150" alt="Profile Picture">
            <h2 class="mt-2"><?= htmlspecialchars($user['Username']) ?></h2>
            <p>Email: <?= htmlspecialchars($user['Email']) ?></p>
        </div>
    </div>
</div>

<script src="../../assets/libraries/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
