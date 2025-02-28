<?php
session_start();
require_once '../../connection/db.php';

// Disable error display in production
error_reporting(0);
ini_set('display_errors', 0);

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Initialize variables
$error = null;
$success = null;

// Fetch user details from the database
$userID = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT UserID, Username, FirstName, LastName, Email, PhoneNumber, 
           Address, ProfilePictureURL, Role, Status, 
           DATE_FORMAT(LastLogin, '%M %d, %Y %h:%i %p') as LastLoginFormatted
    FROM User 
    WHERE UserID = :userID
");
$stmt->execute([':userID' => $userID]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: login.php?error=' . urlencode('User not found'));
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Validate input
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $phoneNumber = trim($_POST['phoneNumber']);
        $address = trim($_POST['address']);
        $password = trim($_POST['password']);
        $confirmPassword = trim($_POST['confirmPassword']);

        // Validation checks
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        if (!empty($password)) {
            if (strlen($password) < 8) {
                throw new Exception("Password must be at least 8 characters long");
            }
            if ($password !== $confirmPassword) {
                throw new Exception("Passwords do not match");
            }
        }

        // Handle profile picture upload
        $profilePictureURL = $user['ProfilePictureURL'];
        if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($_FILES['profilePicture']['type'], $allowedTypes)) {
                throw new Exception("Invalid file type. Only JPG, PNG and GIF are allowed");
            }

            if ($_FILES['profilePicture']['size'] > $maxSize) {
                throw new Exception("File is too large. Maximum size is 5MB");
            }

            $uploadDir = '../../uploads/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '_' . basename($_FILES['profilePicture']['name']);
            $filePath = $uploadDir . $fileName;

            if (!move_uploaded_file($_FILES['profilePicture']['tmp_name'], $filePath)) {
                throw new Exception("Failed to upload profile picture");
            }

            // Delete old profile picture if it exists
            if (!empty($profilePictureURL) && file_exists('../../' . $profilePictureURL)) {
                unlink('../../' . $profilePictureURL);
            }

            $profilePictureURL = 'uploads/profile_pictures/' . $fileName;
        }

        // Begin transaction
        $conn->beginTransaction();

        // Update basic information
        $stmt = $conn->prepare("
            UPDATE User 
            SET FirstName = :firstName, 
                LastName = :lastName, 
                Email = :email, 
                PhoneNumber = :phoneNumber, 
                Address = :address, 
                ProfilePictureURL = :profilePictureURL,
                LastModified = CURRENT_TIMESTAMP
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

        // Commit transaction
        $conn->commit();
        $success = "Profile updated successfully!";

        // Refresh user data
        $stmt = $conn->prepare("
            SELECT UserID, Username, FirstName, LastName, Email, PhoneNumber, 
                   Address, ProfilePictureURL, Role, Status,
                   DATE_FORMAT(LastLogin, '%M %d, %Y %h:%i %p') as LastLoginFormatted
            FROM User 
            WHERE UserID = :userID
        ");
        $stmt->execute([':userID' => $userID]);
        $user = $stmt->fetch();

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - ELCHEF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
    <style>
        .profile-header {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .profile-name {
            font-size: 1.5rem;
            margin: 1rem 0 0.5rem;
        }

        .profile-role {
            font-size: 1rem;
            color: #6c757d;
        }

        .profile-stats {
            padding: 1rem;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .preview-image {
            max-width: 150px;
            max-height: 150px;
            display: none;
            margin-top: 1rem;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>ELCHEF Admin</h3>
            </div>
            <ul class="list-unstyled components">
                <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a></li>
                <li><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <button type="button" id="sidebarToggle" class="btn btn-info">
                <i class="fas fa-bars"></i>
            </button>

            <div class="main-content">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Profile Header -->
                <div class="profile-header text-center">
                    <img src="../../<?php echo !empty($user['ProfilePictureURL']) ? htmlspecialchars($user['ProfilePictureURL']) : 'uploads/profile_pictures/default.jpg'; ?>"
                        alt="Profile Picture" class="profile-picture">
                    <h2 class="profile-name">
                        <?php echo htmlspecialchars(trim($user['FirstName'] . ' ' . $user['LastName'])) ?: htmlspecialchars($user['Username']); ?>
                    </h2>
                    <div class="profile-role">
                        <span class="badge bg-<?php echo $user['Role'] === 'admin' ? 'danger' : 'primary'; ?>">
                            <?php echo ucfirst(htmlspecialchars($user['Role'])); ?>
                        </span>
                        <span class="badge bg-<?php echo $user['Status'] === 'active' ? 'success' : 'secondary'; ?>">
                            <?php echo ucfirst(htmlspecialchars($user['Status'])); ?>
                        </span>
                    </div>
                    <?php if ($user['LastLoginFormatted']): ?>
                        <small class="text-muted mt-2 d-block">
                            Last login: <?php echo htmlspecialchars($user['LastLoginFormatted']); ?>
                        </small>
                    <?php endif; ?>
                </div>

                <!-- Profile Form -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Edit Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="<?php echo htmlspecialchars($user['Username']); ?>" readonly>
                                    <small class="text-muted">Username cannot be changed</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="profilePicture" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" id="profilePicture" name="profilePicture"
                                        accept="image/jpeg,image/png,image/gif">
                                    <small class="text-muted">Max file size: 5MB. Allowed formats: JPG, PNG, GIF</small>
                                    <img id="picturePreview" src="#" alt="Preview" class="preview-image">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName"
                                        value="<?php echo htmlspecialchars($user['FirstName']); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName"
                                        value="<?php echo htmlspecialchars($user['LastName']); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?php echo htmlspecialchars($user['Email']); ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phoneNumber" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber"
                                        value="<?php echo htmlspecialchars($user['PhoneNumber']); ?>">
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control" id="address" name="address"
                                        rows="3"><?php echo htmlspecialchars($user['Address']); ?></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                        minlength="8">
                                    <small class="text-muted">Leave blank to keep current password</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword"
                                        name="confirmPassword" minlength="8">
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Profile picture preview
            const profilePicture = document.getElementById('profilePicture');
            const picturePreview = document.getElementById('picturePreview');

            profilePicture.addEventListener('change', function (e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        picturePreview.style.display = 'block';
                        picturePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            });

            // Auto-close alerts after 5 seconds
            setTimeout(function () {
                document.querySelectorAll('.alert').forEach(function (alert) {
                    if (alert && typeof bootstrap !== 'undefined') {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);

            // Form validation
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');

                // Password match validation
                const password = document.getElementById('password');
                const confirmPassword = document.getElementById('confirmPassword');
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Passwords don't match");
                } else {
                    confirmPassword.setCustomValidity('');
                }
            });

            // Password match validation on input
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirmPassword');
            confirmPassword.addEventListener('input', function () {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity("Passwords don't match");
                } else {
                    confirmPassword.setCustomValidity('');
                }
            });
        });
    </script>
</body>

</html>