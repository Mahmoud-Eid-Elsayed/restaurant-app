<?php
require_once '../../connection/db.php';

class User {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }


    private function validateUserData($data, $isUpdate = false) {
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


    public function addUser($data, $profilePic) {
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
                $uploadDir = "uploads/";
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


    public function updateUser($user_id, $username, $email, $phone, $address, $profilePicture = null) {
        $errors = $this->validateUserData(compact('username', 'email', 'phone', 'address'), true);
        if (!empty($errors)) {
            return false;
        }

        $query = "UPDATE User SET Username = ?, Email = ?, PhoneNumber = ?, Address = ?";
        $params = [$username, $email, $phone, $address];

        if ($profilePicture && $profilePicture['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($profilePicture['type'], $allowedTypes)) {
                $uploadDir = "uploads/";
                $imageName = time() . "_" . basename($profilePicture['name']);
                $uploadPath = $uploadDir . $imageName;
                move_uploaded_file($profilePicture['tmp_name'], $uploadPath);

                $query .= ", ProfilePictureURL = ?";
                $params[] = $uploadPath;
            } else {
                return false;
            }
        }

        $query .= " WHERE UserID = ?";
        $params[] = $user_id;

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($params);
    }
    public function getUserById($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM User WHERE UserID = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function loginUser($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM User WHERE Email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['Password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['role'] = $user['Role'];
            $_SESSION['profile_picture'] = $user['ProfilePictureURL'];
    
            
            $updateStmt = $this->conn->prepare("UPDATE User SET LastLoginDate = NOW() WHERE UserID = :id");
            $updateStmt->execute(['id' => $user['UserID']]);
    
            return ['status' => true, 'role' => $user['Role']];
        }
        return ['status' => false, 'message' => 'Invalid email or password.'];
    }
    
}
?>
