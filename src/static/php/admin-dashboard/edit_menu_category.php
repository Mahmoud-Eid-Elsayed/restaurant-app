<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: menu_categories.php");
  exit();
}

$categoryId = $_GET['id'];

// Fetch category details
$stmt = $conn->prepare("SELECT * FROM MenuCategory WHERE CategoryID = :id");
$stmt->execute([':id' => $categoryId]);
$category = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$category) {
  header("Location: menu_categories.php");
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $categoryName = $_POST['categoryName'];
  $description = $_POST['description'];

  $stmt = $conn->prepare("UPDATE MenuCategory SET CategoryName = :categoryName, Description = :description WHERE CategoryID = :id");
  $stmt->execute([
    ':categoryName' => $categoryName,
    ':description' => $description,
    ':id' => $categoryId
  ]);

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

    <div id="content">
      <div class="header">

      </div>
      <div class="main-content">
        <h2>Edit Menu Category</h2>
        <form method="POST">
          <div class="mb-3">
            <label for="categoryName" class="form-label">Category Name</label>
            <input type="text" class="form-control" id="categoryName" name="categoryName"
              value="<?php echo $category['CategoryName']; ?>" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"
              rows="3"><?php echo $category['Description']; ?></textarea>
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