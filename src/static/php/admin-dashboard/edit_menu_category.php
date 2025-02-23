<?php
require_once '../../connection/db.php';

// Check if the category ID is provided
if (!isset($_GET['id'])) {
  header("Location: menu_categories.php");
  exit();
}

$categoryId = $_GET['id'];

// Fetch category details
$stmt = $conn->prepare("SELECT * FROM MenuCategory WHERE CategoryID = :id");
$stmt->execute([':id' => $categoryId]);
$category = $stmt->fetch(); // Use fetch() instead of fetchAll()

// Check if the category exists
if (!$category) {
  header("Location: menu_categories.php");
  exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $categoryName = $_POST['categoryName'];
  $description = $_POST['description'];

  // Update the category in the database
  $stmt = $conn->prepare("UPDATE MenuCategory SET CategoryName = :categoryName, Description = :description WHERE CategoryID = :id");
  $stmt->execute([
    ':categoryName' => $categoryName,
    ':description' => $description,
    ':id' => $categoryId
  ]);

  // Redirect to the categories page
  header("Location: menu_categories.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Menu Category - ELCHEF</title>
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
        <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
        <li><a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a></li>
        <li><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
        <li class="active"><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
        <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
        <li><a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a></li>
        <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a>

      </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
      <button type="button" id="sidebarToggle">
        <i class="fas fa-bars"></i>
      </button>

      <div id="content">
        <div class="header">
          <!-- Header content here -->
        </div>
        <div class="main-content">
          <h2>Edit Menu Category</h2>
          <form method="POST">
            <div class="mb-3">
              <label for="categoryName" class="form-label">Category Name</label>
              <input type="text" class="form-control" id="categoryName" name="categoryName"
                value="<?php echo htmlspecialchars($category['CategoryName']); ?>" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="description"
                rows="3"><?php echo htmlspecialchars($category['Description']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
</body>

</html>