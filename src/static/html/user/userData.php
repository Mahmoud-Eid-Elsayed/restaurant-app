<?php
session_start();
require '../../connection/db.php';
require '../../php/user/user.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = new User($conn);
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
    <title>User Profile</title>
    <link rel="stylesheet" href="../../../assets/libraries/fontawesome-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
        }
        .user-info {
            margin-top: 15px;
        }
        .user-info h3 {
            color: #333;
        }
        .btn-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>
        <div class="container">
            <div class="profile-container">
                <img src="<?= htmlspecialchars($userData['ProfilePictureURL'] ?: 'default-avatar.png') ?>" alt="Profile Picture" class="profile-img">
                <div class="user-info">
                    <h3><?= htmlspecialchars($userData['Username']) ?></h3>
                    <p><strong>firstname:</strong> <?= htmlspecialchars($userData['FirstName']) ?></p>
                    <p><strong>lastname:</strong> <?= htmlspecialchars($userData['LastName']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($userData['Email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($userData['PhoneNumber'] ?: 'N/A') ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($userData['Address'] ?: 'N/A') ?></p>
                </div>
                <div class="btn-container">
                    <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
