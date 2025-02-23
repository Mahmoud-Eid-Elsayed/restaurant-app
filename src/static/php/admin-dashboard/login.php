<?php
session_start();
require_once '../../connection/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM User WHERE Username = :username AND Role = 'Staff'");
  $stmt->bindParam(':username', $username);
  $stmt->execute();
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['Password'])) {
    $_SESSION['user_id'] = $user['UserID'];
    $_SESSION['username'] = $user['Username'];
    $_SESSION['role'] = $user['Role'];

    header('Location: index.php');
    exit();
  } else {
    $error = "Invalid username or password.";
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <h2 class="text-center">Admin Login</h2>
        <?php if (isset($error))
          echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary">Login</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>