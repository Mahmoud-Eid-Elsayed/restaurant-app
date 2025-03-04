
# **Forgot Password Setup (PHP & SendGrid)**  

## **1️⃣ Install Dependencies**  
Make sure you have **Composer** installed. Then, run:  
```sh
composer require phpmailer/phpmailer vlucas/phpdotenv
```

---

## **2️⃣ Configure `.env` File**  
Create a `.env` file in your project **root** and add:  
```env
MAIL_USERNAME=apikey  # Always set to 'apikey'
SENDGRID_API_KEY=your_sendgrid_api_key_here
MAIL_FROM_EMAIL=macawilo@asciibinder.net
MAIL_FROM_NAME="El Chef"
APP_URL=http://localhost/restaurant-app
```
✅ **Make sure `MAIL_FROM_NAME` is inside double quotes ("")** to avoid errors.  

---

## **3️⃣ Database Setup**  
Run this SQL query to create the required tables:  
```sql
CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## **4️⃣ Setup Database Connection (`db.php`)**  
Edit your `db.php` file:  
```php
<?php
$host = 'localhost';
$port = '3306';
$dbname = 'Restaurant_DB';
$username = 'your_db_username';
$password = 'your_db_password';

try {
    $conn = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
```

---

## **5️⃣ Forgot Password Form (`forgot_password.php`)**  
```php
<?php
session_start();
require '../../../../src/static/connection/db.php';
require __DIR__ . '/../../../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../');
$dotenv->load();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate a token
        $token = bin2hex(random_bytes(50));

        // Save token in the database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->execute([$email, $token]);

        // Send Reset Email
        $resetLink = $_ENV['APP_URL'] . "/src/static/html/user/reset_password.php?token=$token";
        $mail = new PHPMailer(true);

        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host = 'smtp.sendgrid.net';
            $mail->SMTPAuth = true;
            $mail->Username = 'apikey';
            $mail->Password = $_ENV['SENDGRID_API_KEY'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email content
            $mail->setFrom($_ENV['MAIL_FROM_EMAIL'], $_ENV['MAIL_FROM_NAME']);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Password Reset Request";
            $mail->Body = "Click here to reset your password: <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            echo "Password reset link sent!";
        } catch (Exception $e) {
            echo "Email sending failed: " . $mail->ErrorInfo;
        }
    } else {
        echo "Email not found!";
    }
}
?>
```
✅ **This file will send a password reset link to the user.**

---

## **6️⃣ Password Reset Page (`reset_password.php`)**  
```php
<?php
session_start();
require '../../../../src/static/connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Get email from token
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Update password
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$newPassword, $user['email']]);

        // Delete the token
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$user['email']]);

        echo "Password successfully reset!";
    } else {
        echo "Invalid token!";
    }
}
?>

<form method="post">
    <input type="hidden" name="token" value="<?= $_GET['token'] ?>">
    <label>New Password</label>
    <input type="password" name="password" required>
    <button type="submit">Reset Password</button>
</form>
```
✅ **Users can enter a new password using this form.**

---

## **7️⃣ Test the System**
1. Open `forgot_password.php` and enter an email.  
2. Check your **email inbox** for the reset link.  
3. Click the reset link and enter a **new password**.  
4. Your password is now **updated** in the database! 🎉  

---

## **8️⃣ Troubleshooting**
❌ **"Could not authenticate" error?**  
- Make sure `SENDGRID_API_KEY` is correct in `.env`.  
- Try port **2525** instead of **587**.  

❌ **Emails not received?**  
- Check **spam/junk folder**.  
- Use a **verified sender email** in SendGrid.  
