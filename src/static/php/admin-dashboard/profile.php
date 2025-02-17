<?php
session_start();
require_once '../../connection/db.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details from the database
$userID = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT UserID, Username, FirstName, LastName, Email, PhoneNumber, Address, ProfilePictureURL FROM User WHERE UserID = :userID");
$stmt->execute([':userID' => $userID]);
$user = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found in the database. UserID: $userID";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phoneNumber = $_POST['phoneNumber'];
    $address = $_POST['address'];
    $password = $_POST['password'];


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Handle profile picture upload
        $profilePictureURL = $user['ProfilePictureURL'] ?? 'uploads/profile_pictures/default.jpg'; // Use existing or default image
        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
            $uploadDir = '../../uploads/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
            }

            $fileName = basename($_FILES['profilePicture']['name']);
            $filePath = $uploadDir . $fileName;

            // Move the uploaded file to the upload directory
            if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $filePath)) {
                $profilePictureURL = 'uploads/profile_pictures/' . $fileName;
            } else {
                $error = "Failed to upload profile picture.";
            }
        }

        // Update basic information
        $stmt = $conn->prepare("
            UPDATE User 
            SET FirstName = :firstName, 
                LastName = :lastName, 
                Email = :email, 
                PhoneNumber = :phoneNumber, 
                Address = :address, 
                ProfilePictureURL = :profilePictureURL 
            WHERE UserID = :userID
        ");
        $stmt->execute([
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':email' => $email,
            ':phoneNumber' => $phoneNumber,
            ':address' => $address,
            ':profilePictureURL' => $profilePictureURL,
            ':userID' => $userID
        ]);

        // Update password if provided
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE User SET Password = :password WHERE UserID = :userID");
            $stmt->execute([':password' => $hashedPassword, ':userID' => $userID]);
        }

        $success = "Profile updated successfully!";

        // Refresh user data after update
        $stmt = $conn->prepare("SELECT UserID, Username, FirstName, LastName, Email, PhoneNumber, Address, ProfilePictureURL FROM User WHERE UserID = :userID");
        $stmt->execute([':userID' => $userID]);
        $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - ELCHEF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
</head>

<body>
    <div class="wrapper">
        <!-- Include the same sidebar and header as in index.php -->
        <div id="content">
            <div class="header">

            </div>
            <div class="main-content">
                <h1>Profile</h1>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="<?php echo htmlspecialchars($user['Username'] ?? ''); ?>" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="firstName"
                                    value="<?php echo htmlspecialchars($user['FirstName'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="lastName"
                                    value="<?php echo htmlspecialchars($user['LastName'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="phoneNumber" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber"
                                    value="<?php echo htmlspecialchars($user['PhoneNumber'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address"
                                    rows="3"><?php echo htmlspecialchars($user['Address'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="profilePicture" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="profilePicture" name="profilePicture">
                                <?php if (!empty($user['ProfilePictureURL'])): ?>
                                    <img src="../../<?php echo htmlspecialchars($user['ProfilePictureURL']); ?>"
                                        alt="Profile Picture" class="img-thumbnail mt-2" style="max-width: 150px;">
                                <?php endif; ?>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
</body>

</html>