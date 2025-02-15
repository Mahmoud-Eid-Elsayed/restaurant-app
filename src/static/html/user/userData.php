<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM User WHERE UserID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
                <img src="<?= htmlspecialchars($user['ProfilePictureURL'] ?: 'default-avatar.png') ?>" alt="Profile Picture" class="profile-img">
                <div class="user-info">
                    <h3><?= htmlspecialchars($user['Username']) ?></h3>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['PhoneNumber'] ?: 'N/A') ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($user['Address'] ?: 'N/A') ?></p>
                </div>
                <div class="btn-container">
                    <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
