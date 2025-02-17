<?php
require_once '../../connection/db.php';

// Fetch all users
$stmt = $conn->query("SELECT * FROM User");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - ELCHEF</title>
  <!-- Include Bootstrap CSS and Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../../static/css/admin-dashboard.css">
</head>

<body>
  <div class="wrapper">
    <!-- Include the same sidebar and header as in index.php -->
    <div id="content">
      <div class="header">
        <!-- Header content -->
      </div>
      <div class="main-content">
        <h2>Manage Users</h2>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Role</th>
              <th>Email</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo $user['UserID']; ?></td>
                <td><?php echo $user['Username']; ?></td>
                <td><?php echo $user['Role']; ?></td>
                <td><?php echo $user['Email']; ?></td>
                <td>
                  <a href="edit_user.php?id=<?php echo $user['UserID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="delete_user.php?id=<?php echo $user['UserID']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

=======
<?php
require_once '../../connection/db.php';

// Fetch all users
$stmt = $conn->query("SELECT * FROM User");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Users - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
</head>

<body>
  <div class="wrapper">
    <!-- Include the same sidebar and header as in index.php -->
    <div id="content">
      <div class="header">
        <!-- Header content -->
      </div>
      <div class="main-content">
        <h2>Manage Users</h2>
        <a href="add_user.php" class="btn btn-primary mb-3">Add New User</a>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Role</th>
              <th>Email</th>
              <th>profile picture</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?php echo $user['UserID']; ?></td>
                <td><?php echo $user['Username']; ?></td>
                <td><?php echo $user['Role']; ?></td>
                <td><?php echo $user['Email']; ?></td>
                <td><?php echo $user['ProfilePictureURL']; ?></td>
                <td>
                  <a href="edit_user.php?id=<?php echo $user['UserID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="delete_user.php?id=<?php echo $user['UserID']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
  <script src="../../js/admin-dashboard.js"></script>
</body>

</html>