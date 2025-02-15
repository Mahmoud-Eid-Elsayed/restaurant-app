<?php
session_start();
require '../../connection/db.php';
require_once '../../php/user/user.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user = new User($conn);
$user_id = $_SESSION['user_id'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $profilePic = isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK ? $_FILES['profile_picture'] : null;
    
    $success = $user->updateUser($user_id, $username, $email, $phone, $address, $profilePic);
    
    if ($success) {
        $_SESSION['message'] = "Your profile has been updated successfully!";
        echo "<script>
                alert('Your profile has been updated successfully!');
                window.location.href = 'userdata.php';
              </script>";
        exit();
    } else {
        $errors[] = "Failed to update profile.";
    }
}

$userData = $user->getUserById($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Edit Profile</title>
</head>
<body>
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>
        <div class="container mt-4">
            <h2>Edit Profile</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Username:</label>
                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($userData['Username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email:</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($userData['Email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Phone:</label>
                    <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($userData['PhoneNumber']) ?>">
                </div>
                <div class="mb-3">
                    <label>Address:</label>
                    <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($userData['Address']) ?>">
                </div>
                <div class="mb-3">
                    <label>Profile Picture:</label>
                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                    <?php if ($userData['ProfilePictureURL']): ?>
                        <img src="<?= htmlspecialchars($userData['ProfilePictureURL']) ?>" alt="Current Profile Picture" class="img-thumbnail mt-2" width="150">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
