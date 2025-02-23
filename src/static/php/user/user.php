<?php
require_once '../../connection/db.php';

class User
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    private function validateUserData($data, $isUpdate = false)
    {
        $errors = [];

        if (!$isUpdate || isset($data['firstname'])) {
            if (!preg_match("/^[a-zA-Z]{2,30}$/", $data['firstname'] ?? '')) {
                $errors['firstname'] = "First name must be 2-30 letters long.";
            }
        }
        if (!$isUpdate || isset($data['lastname'])) {
            if (!preg_match("/^[a-zA-Z]{2,30}$/", $data['lastname'] ?? '')) {
                $errors['lastname'] = "Last name must be 2-30 letters long.";
            }
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Invalid email address.";
        }
        if (!preg_match("/^\d{10,15}$/", $data['phonenumber'])) {
            $errors['phonenumber'] = "Phone number must be 10-15 digits.";
        }
        if (!$isUpdate || isset($data['username'])) {
            if (!preg_match("/^[a-zA-Z0-9_]{3,16}$/", $data['username'] ?? '')) {
                $errors['username'] = "Username must be 3-16 characters long.";
            }
        }
        return $errors;
    }


    public function addUser($data, $profilePic)
    {
        $errors = $this->validateUserData($data);

        if (!empty($errors)) {
            return ['status' => false, 'errors' => $errors];
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM User WHERE email = :email OR username = :username");
        $stmt->execute(['email' => $data['email'], 'username' => $data['username']]);
        if ($stmt->fetchColumn() > 0) {
            return ['status' => false, 'errors' => ['email' => "Email or username already exists."]];
        }

        $uploadPath = null;
        if ($profilePic && $profilePic['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($profilePic['type'], $allowedTypes)) {
                $uploadDir = "../../../assets/images/users/uploads/";
                $imageName = time() . "_" . basename($profilePic['name']);
                $uploadPath = $uploadDir . $imageName;
                move_uploaded_file($profilePic['tmp_name'], $uploadPath);
            } else {
                return ['status' => false, 'errors' => ['profile_pic' => "Only JPEG, PNG, and GIF images are allowed."]];
            }
        }

        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        $timestamp = date("Y-m-d H:i:s");

        $stmt = $this->conn->prepare("INSERT INTO User (Username, Password, Role, FirstName, LastName, Email, PhoneNumber, Address, ProfilePictureURL, RegistrationDate, LastLoginDate)
                                      VALUES (:username, :password, 'customer', :firstname, :lastname, :email, :phonenumber, :address, :ProfilePictureURL, :RegistrationDate, :lastlogindate)");

        $success = $stmt->execute([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'address' => $data['address'],
            'email' => $data['email'],
            'phonenumber' => $data['phonenumber'],
            'username' => $data['username'],
            'password' => $passwordHash,
            'ProfilePictureURL' => $uploadPath,
            'RegistrationDate' => $timestamp,
            'lastlogindate' => $timestamp
        ]);

        return ['status' => $success, 'errors' => []];
    }


    public function updateUser($user_id, $username, $email, $phone, $address, $profilePic = null)
    {
        try {
            $profilePicPath = null;

            if ($profilePic && $profilePic['error'] === UPLOAD_ERR_OK) {
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (in_array($profilePic['type'], $allowedTypes)) {
                    $uploadDir = "../../../assets/images/users/uploads/";

                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $imageExtension = pathinfo($profilePic['name'], PATHINFO_EXTENSION);
                    $imageName = "user_" . $user_id . "." . $imageExtension;
                    $profilePicPath = $uploadDir . $imageName;

                    if (!move_uploaded_file($profilePic['tmp_name'], $profilePicPath)) {
                        return false;
                    }
                } else {
                    return false;
                }
            }

            if ($profilePicPath) {
                $stmt = $this->conn->prepare("UPDATE User SET Username = ?, Email = ?, PhoneNumber = ?, Address = ?, ProfilePictureURL = ? WHERE UserID = ?");
                $stmt->execute([$username, $email, $phone, $address, $profilePicPath, $user_id]);
            } else {
                $stmt = $this->conn->prepare("UPDATE User SET Username = ?, Email = ?, PhoneNumber = ?, Address = ? WHERE UserID = ?");
                $stmt->execute([$username, $email, $phone, $address, $user_id]);
            }

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }


    public function getUserById($user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM User WHERE UserID = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function loginUser($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE Email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['Password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['Role'];

            return ['status' => true, 'role' => $user['Role']];
        }
        return ['status' => false, 'message' => 'Invalid email or password.'];
    }


}
?>