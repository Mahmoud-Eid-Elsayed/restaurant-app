<?php
require_once '../../connection/db.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header('Location: users.php?error=' . urlencode('No user ID provided'));
    exit;
}

$userId = (int)$_GET['id'];
$error = null;
$success = null;

// Fetch the user's current data
try {
    $stmt = $conn->prepare("SELECT * FROM User WHERE UserID = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: users.php?error=' . urlencode('User not found'));
        exit;
    }
} catch (PDOException $e) {
    header('Location: users.php?error=' . urlencode('Error fetching user data: ' . $e->getMessage()));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = trim($_POST['username']);
        $role = $_POST['role'];
        $firstName = trim($_POST['firstName']);
        $lastName = trim($_POST['lastName']);
        $email = trim($_POST['email']);
        $phoneNumber = trim($_POST['phoneNumber']);
        $address = trim($_POST['address']);

        // Validate username
        if (empty($username)) {
            throw new Exception('Username cannot be empty');
        }

        // Check if username exists for any other user
        $checkStmt = $conn->prepare("SELECT UserID FROM User WHERE Username = ? AND UserID != ?");
        $checkStmt->execute([$username, $userId]);
        if ($checkStmt->fetch()) {
            throw new Exception('Username already exists. Please choose a different username.');
        }

        // Validate role
        $validRoles = ['Staff', 'Customer']; // Match exact database ENUM values
        if (!in_array($role, $validRoles)) {
            throw new Exception('Invalid role selected');
        }

        // Validate email if provided
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Update the user
        $stmt = $conn->prepare("
            UPDATE User 
            SET 
                Username = ?,
                Role = ?,
                FirstName = ?,
                LastName = ?,
                Email = ?,
                PhoneNumber = ?,
                Address = ?
            WHERE UserID = ?
        ");

        $stmt->execute([
            $username,
            $role,
            $firstName ?: null,
            $lastName ?: null,
            $email ?: null,
            $phoneNumber ?: null,
            $address ?: null,
            $userId
        ]);

        header('Location: users.php?message=' . urlencode('User updated successfully'));
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
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
        <li class="active"><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Edit User</h2>
          <a href="users.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
          </a>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($error); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>

        <div class="card">
          <div class="card-body">
            <form method="POST" class="needs-validation" novalidate>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="username" class="form-label">Username *</label>
                  <input type="text" class="form-control" id="username" name="username" 
                         value="<?php echo htmlspecialchars($user['Username']); ?>" required>
                  <div class="invalid-feedback">Username is required</div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="role" class="form-label">Role *</label>
                  <select class="form-select" id="role" name="role" required>
                    <option value="Staff" <?php echo $user['Role'] === 'Staff' ? 'selected' : ''; ?>>Staff</option>
                    <option value="Customer" <?php echo $user['Role'] === 'Customer' ? 'selected' : ''; ?>>Customer</option>
                  </select>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="firstName" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="firstName" name="firstName" 
                         value="<?php echo htmlspecialchars($user['FirstName'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                  <label for="lastName" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="lastName" name="lastName" 
                         value="<?php echo htmlspecialchars($user['LastName'] ?? ''); ?>">
                </div>

                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" 
                         value="<?php echo htmlspecialchars($user['Email'] ?? ''); ?>">
                  <div class="invalid-feedback">Please enter a valid email address</div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="phoneNumber" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" 
                         value="<?php echo htmlspecialchars($user['PhoneNumber'] ?? ''); ?>">
                </div>

                <div class="col-12 mb-3">
                  <label for="address" class="form-label">Address</label>
                  <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($user['Address'] ?? ''); ?></textarea>
                </div>
              </div>

              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="users.php" class="btn btn-light me-md-2">Cancel</a>
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save"></i> Save Changes
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
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.querySelector('.needs-validation');
      
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      });
    });
  </script>
</body>

</html>