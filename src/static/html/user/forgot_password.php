<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require '../../../../src/static/connection/db.php';
require __DIR__ . '/../../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);

  // ✅ Check if email is empty
  if (empty($email)) {
    echo "❌ Email is required!";
    exit;
  }

  // ✅ Check if email exists in users table
  $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user) {
    // ✅ Generate a unique reset token
    $token = bin2hex(random_bytes(50));

    // ✅ Insert the token into the `password_resets` table
    $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
    $stmt->execute([$email, $token]);

    // ✅ Send reset link via SendGrid SMTP using PHPMailer
    $resetLink = $_ENV['APP_URL'] . "/src/static/html/user/reset_password.php?token=$token";
    $subject = "Password Reset Request";
    $body = "Click here to reset your password: <a href='$resetLink'>$resetLink</a>";

    // ✅ Set up PHPMailer with SendGrid
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.sendgrid.net';
      $mail->SMTPAuth = true;
      $mail->Username = $_ENV['MAIL_USERNAME']; // 'apikey'
      $mail->Password = $_ENV['SENDGRID_API_KEY'];
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;
      $mail->SMTPDebug = 0;

      $mail->setFrom($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);
      $mail->addAddress($email);
      $mail->addReplyTo($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $body;

      $mail->send();
      echo "✅ Password reset link sent!";
    } catch (Exception $e) {
      echo "❌ Email sending failed: " . $mail->ErrorInfo;
    }
  } else {
    echo "❌ Email not found!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>Forgot Password</title>
  <link rel="stylesheet" href="../../../assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css">
</head>

<body>
  <div class="container">
    <h2>Forgot Password</h2>
    <form method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" class="form-control" name="email" required>
      </div>
      <button type="submit" class="btn btn-primary">Send Reset Link</button>
    </form>
  </div>
</body>

</html>