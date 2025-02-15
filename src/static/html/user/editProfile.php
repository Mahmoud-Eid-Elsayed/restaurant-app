<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $profilePicture = $_FILES['profile_picture'] ?? null;

    if (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Username must be between 3 and 50 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($phone) && (!ctype_digit($phone) || strlen($phone) < 8 || strlen($phone) > 15)) {
        $errors[] = "Phone number must contain only numbers and be between 8 and 15 digits.";
    }

    if (!empty($address) && strlen($address) > 255) {
        $errors[] = "Address must be less than 255 characters.";
    }

    $profilePicturePath = null;
    if ($profilePicture && $profilePicture['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($profilePicture['type'], $allowedTypes)) {
            $errors[] = "Only JPG, PNG, and WEBP images are allowed.";
        } else {
            $uploadDir = "uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $extension = pathinfo($profilePicture['name'], PATHINFO_EXTENSION);
            $profilePicturePath = $uploadDir . "user_" . $user_id . "." . $extension;

            if (!move_uploaded_file($profilePicture['tmp_name'], $profilePicturePath)) {
                $errors[] = "Failed to upload the image.";
            }
        }
    }

    if (empty($errors)) {
        try {
            if ($profilePicturePath) {
                $stmt = $conn->prepare("UPDATE User SET Username = ?, Email = ?, PhoneNumber = ?, Address = ?, ProfilePictureURL = ? WHERE UserID = ?");
                $stmt->execute([$username, $email, $phone, $address, $profilePicturePath, $user_id]);
                $_SESSION['profile_picture'] = $profilePicturePath; 
            } else {
                $stmt = $conn->prepare("UPDATE User SET Username = ?, Email = ?, PhoneNumber = ?, Address = ? WHERE UserID = ?");
                $stmt->execute([$username, $email, $phone, $address, $user_id]);
            }

            $_SESSION['message'] = "Your profile has been updated successfully!";
            echo "<script>
                    alert('Your profile has been updated successfully!');
                    window.location.href = 'userdata.php';
                  </script>";
            exit();
        } catch (PDOException $e) {
            $errors[] = "Error updating profile: " . $e->getMessage();
        }
    }
}

$stmt = $conn->prepare("SELECT * FROM User WHERE UserID = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
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
                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['Username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Email:</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['Email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label>Phone:</label>
                    <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($user['PhoneNumber']) ?>">
                </div>
                <div class="mb-3">
                    <label>Address:</label>
                    <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($user['Address']) ?>">
                </div>
                <div class="mb-3">
                    <label>Profile Picture:</label>
                    <input type="file" class="form-control" name="profile_picture" accept="image/*">
                    <?php if ($user['ProfilePictureURL']): ?>
                        <img src="<?= htmlspecialchars($user['ProfilePictureURL']) ?>" alt="Current Profile Picture" class="img-thumbnail mt-2" width="150">
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
