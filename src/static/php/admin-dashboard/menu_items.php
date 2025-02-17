<?php
require_once '../../connection/db.php';

// Fetch all menu items with category names
$stmt = $conn->query("
    SELECT MenuItem.*, MenuCategory.CategoryName 
    FROM MenuItem 
    INNER JOIN MenuCategory ON MenuItem.CategoryID = MenuCategory.CategoryID
");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Menu Items - ELCHEF</title>
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
        <h2>Manage Menu Items</h2>
        <a href="add_menu_item.php" class="btn btn-primary mb-3">Add New Item</a>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Item Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Availability</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($menuItems as $item): ?>
              <tr>
                <td><?php echo $item['ItemID']; ?></td>
                <td><?php echo $item['ItemName']; ?></td>
                <td><?php echo $item['CategoryName']; ?></td>
                <td>$<?php echo number_format($item['Price'], 2); ?></td>
                <td><?php echo $item['Availability'] ? 'Available' : 'Unavailable'; ?></td>
                <td>
                  <a href="edit_menu_item.php?id=<?php echo $item['ItemID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                  <a href="delete_menu_item.php?id=<?php echo $item['ItemID']; ?>"
                    class="btn btn-danger btn-sm">Delete</a>
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