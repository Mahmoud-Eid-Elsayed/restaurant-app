<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
  header('Location: login.php');
  exit();
}
include '../../connection/db.php';

$userId = $_GET['id'];

// Fetch the user's current data
$stmt = $conn->prepare("SELECT * FROM User WHERE UserID = :userId");
$stmt->bindParam(':userId', $userId);
$stmt->execute();
$user = $stmt->fetch(); // Corrected: No argument passed to fetch()

if (!$user) {
  $_SESSION['error'] = "User not found.";
  header('Location: users.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $role = $_POST['role'];
  $firstName = $_POST['firstName'];
  $lastName = $_POST['lastName'];
  $email = $_POST['email'];
  $phoneNumber = $_POST['phoneNumber'];
  $address = $_POST['address'];

  // Update the user in the database
  $stmt = $conn->prepare("UPDATE User SET Username = :username, Role = :role, FirstName = :firstName, LastName = :lastName, Email = :email, PhoneNumber = :phoneNumber, Address = :address WHERE UserID = :userId");
  $stmt->bindParam(':username', $username);
  $stmt->bindParam(':role', $role);
  $stmt->bindParam(':firstName', $firstName);
  $stmt->bindParam(':lastName', $lastName);
  $stmt->bindParam(':email', $email);
  $stmt->bindParam(':phoneNumber', $phoneNumber);
  $stmt->bindParam(':address', $address);
  $stmt->bindParam(':userId', $userId);

  if ($stmt->execute()) {
    $_SESSION['message'] = "User updated successfully!";
  } else {
    $_SESSION['error'] = "Failed to update user.";
  }

  header('Location: users.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h1>Edit User</h1>
    <form method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['Username']; ?>"
          required>
      </div>
      <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select class="form-control" id="role" name="role" required>
          <option value="Staff" <?php echo $user['Role'] === 'Staff' ? 'selected' : ''; ?>>Staff</option>
          <option value="Customer" <?php echo $user['Role'] === 'Customer' ? 'selected' : ''; ?>>Customer</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="firstName" class="form-label">First Name</label>
        <input type="text" class="form-control" id="firstName" name="firstName"
          value="<?php echo $user['FirstName']; ?>">
      </div>
      <div class="mb-3">
        <label for="lastName" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo $user['LastName']; ?>">
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['Email']; ?>">
      </div>
      <div class="mb-3">
        <label for="phoneNumber" class="form-label">Phone Number</label>
        <input type="text" class="form-control" id="phoneNumber" name="phoneNumber"
          value="<?php echo $user['PhoneNumber']; ?>">
      </div>
      <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control" id="address" name="address" value="<?php echo $user['Address']; ?>">
      </div>
      <button type="submit" class="btn btn-primary">Update User</button>
    </form>
  </div>
</body>

</html>