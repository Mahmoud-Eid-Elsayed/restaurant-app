<?php
session_start();
require '../../../../src/static/connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $token = $_POST['token'];
  $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

  // Check if the token exists
  $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
  $stmt->execute([$token]);
  $resetData = $stmt->fetch();

  if ($resetData) {
    // Update password in users table
    $stmt = $conn->prepare("UPDATE user SET password = ? WHERE email = ?");
    $stmt->execute([$newPassword, $resetData['email']]);

    // Delete token from password_resets
    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$resetData['email']]);

    echo "✅ Password reset successful!";
  } else {
    echo "❌ Invalid or expired token!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Reset Password</title>
  <link rel="stylesheet" href="../../../assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="container">
    <h2>Reset Password</h2>
    <form method="post">
      <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
      <div class="form-group">
        <label>New Password</label>
        <input type="password" class="form-control" name="password" required>
      </div>
      <button type="submit" class="btn btn-success">Reset Password</button>
    </form>
  </div>
</body>

</html>