
<?php
session_start();
$formErrors = $_SESSION['form_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../assets/libraries/fontawesome-6.7.2-web/css/all.min.css">
  <link rel="stylesheet" href="../../../assets/libraries/node_modules/bootstrap/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../css/login&register/signup.css">
    <title> Form</title>
   
</head>
<body >
<div class="container-fluid form-container">
<div class="avatar-container">
        <img src="../../../assets/images/login&register/chef-cash-register_18591-35958.avif" width="80%" alt="Avatar">
    </div>
    <div class="form-wrapper me-5 bg-light">
        <form method="post" action="../../php/user/signupHandle.php" enctype="multipart/form-data">
            <div class="row my-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bolder">First Name</label>
                        <input type="text" class="form-control" name="firstname" placeholder="First Name" value="<?= htmlspecialchars($formData['firstname'] ?? '') ?>" required>
                        <div class="error"> <?= $formErrors['firstname'] ?? '' ?> </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bolder">Last Name</label>
                        <input type="text" class="form-control" name="lastname" placeholder="Last Name" value="<?= htmlspecialchars($formData['lastname'] ?? '') ?>" required>
                        <div class="error"> <?= $formErrors['lastname'] ?? '' ?> </div>
                    </div>
                </div>
            </div>
            <div class="form-group my-3">
                <label class="fw-bolder">Address</label>
                <input type="text" class="form-control" name="address" placeholder="Address" value="<?= htmlspecialchars($formData['address'] ?? '') ?>" required>
                <div class="error"> <?= $formErrors['address'] ?? '' ?> </div>
            </div>
            <div class="row my-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bolder">Email</label>
                        <input type="email" class="form-control" name="email" placeholder="Email" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                        <div class="error"> <?= $formErrors['email'] ?? '' ?> </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bolder">Phone Number</label>
                        <input type="tel" class="form-control" name="phonenumber" placeholder="Phone Number" value="<?= htmlspecialchars($formData['phonenumber'] ?? '') ?>" required>
                        <div class="error"> <?= $formErrors['phonenumber'] ?? '' ?> </div>
                    </div>
                </div>
            </div>
            <div class="row my-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bolder">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Username" value="<?= htmlspecialchars($formData['username'] ?? '') ?>" required>
                        <div class="error"> <?= $formErrors['username'] ?? '' ?> </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bolder">Profile Picture</label>
                        <input type="file" class="form-control" name="profile_pic" required>
                        <div class="error"> <?= $formErrors['profile_pic'] ?? '' ?> </div>
                    </div>
                </div>
            </div>
            <div class="row my-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bolder">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Password" required>
                        <div class="error"> <?= $formErrors['password'] ?? '' ?> </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="fw-bolder">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>
                        <div class="error"> <?= $formErrors['password_confirmation'] ?? '' ?> </div>
                    </div>
                </div>
            </div>
            <div class="form-group form-check my-3">
                <input type="checkbox" class="form-check-input" required>
                <label class="form-check-label">Agree to terms and conditions</label>
            </div>
            <button type="submit" class="btn btn-outline-success btn-block">Submit</button>
        </form>
    </div>

</div>
<script src="../../assets/libraries/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>
