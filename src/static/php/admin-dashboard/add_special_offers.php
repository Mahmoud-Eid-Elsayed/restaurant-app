<?php
require_once '../../connection/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Fetch all menu items
$stmt = $conn->query("SELECT * FROM MenuItem");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $description = $_POST['description'];
  $discountPercentage = $_POST['discountPercentage'];
  $discountAmount = $_POST['discountAmount'];
  $startDate = $_POST['startDate'];
  $expiryDate = $_POST['expiryDate'];
  $itemID = $_POST['itemID'];
  $offerCode = $_POST['offerCode'];
  $imageURL = $offer['ImageURL'];

  // Handle image upload
  if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "../../uploads/Special-offers/"; // Ensure this directory exists and is writable
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
      // Check file size (5MB max)
      if ($_FILES["image"]["size"] <= 8000000) {
        // Allow certain file formats
        if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
          if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $imageURL = "../../uploads/Special-offers/" . basename($_FILES["image"]["name"]);
          } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
          }
        } else {
          echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
          exit();
        }
      } else {
        echo "Sorry, your file is too large.";
        exit();
      }
    } else {
      echo "File is not an image.";
      exit();
    }
  }


  $stmt = $conn->prepare(
    "INSERT INTO specialOffer (Description, DiscountPercentage, DiscountAmount, StartDate, ExpiryDate, ItemID, OfferCode, ImageURL)
        VALUES (:description, :discountPercentage, :discountAmount, :startDate, :expiryDate, :itemID, :offerCode, :imageURL)"
  );
  $stmt->execute([
    ':description' => $description,
    ':discountPercentage' => $discountPercentage,
    ':discountAmount' => $discountAmount,
    ':startDate' => $startDate,
    ':expiryDate' => $expiryDate,
    ':itemID' => $itemID,
    ':offerCode' => $offerCode,
    ':imageURL' => $imageURL
  ]);

  header("Location: special_offers.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Special Offer - ELCHEF</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../../css/admin-dashboard/admin-dashboard.css">
</head>

<body>

  <body>
    <div class="wrapper">
      <nav id="sidebar">
        <div class="sidebar-header">
          <h3>ELCHEF Admin</h3>
        </div>
        <ul class="list-unstyled components">
          <li><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
          <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
          <li><a href="menu_categories.php"><i class="fas fa-list"></i> Categories</a></li>
          <li><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
          <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
          <li class="active"><a href="special_offers.php"><i class="fas fa-tags"></i> Special Offers</a></li>
          <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
          <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
          <li><a href="suppliers.php"><i class="fa-solid fa-truck"></i> Suppliers</a></li>
          <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a></li>
        </ul>
      </nav>
      <div id="content">
        <button type="button" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <div class="main-content">
          <h2>Add Special Offer</h2>
          <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <input type="text" class="form-control" id="description" name="description" required>
            </div>
            <div class="mb-3">
              <label for="discountPercentage" class="form-label">Discount Percentage</label>
              <input type="number" step="0.01" class="form-control" id="discountPercentage" name="discountPercentage">
            </div>
            <div class="mb-3">
              <label for="discountAmount" class="form-label">Discount Amount</label>
              <input type="number" step="0.01" class="form-control" id="discountAmount" name="discountAmount">
            </div>
            <div class="mb-3">
              <label for="startDate" class="form-label">Start Date</label>
              <input type="datetime-local" class="form-control" id="startDate" name="startDate" required>
            </div>
            <div class="mb-3">
              <label for="expiryDate" class="form-label">Expiry Date</label>
              <input type="datetime-local" class="form-control" id="expiryDate" name="expiryDate" required>
            </div>
            <div class="mb-3">
              <label for="itemID" class="form-label">Menu Item</label>
              <select class="form-control" id="itemID" name="itemID" required>
                <?php foreach ($menuItems as $item): ?>
                  <option value="<?php echo $item['ItemID']; ?>"><?php echo $item['ItemName']; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="offerCode" class="form-label">Offer Code</label>
              <input type="text" class="form-control" id="offerCode" name="offerCode">
            </div>
            <div class="mb-3">
              <label for="image" class="form-label">Image</label>
              <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Add Offer</button>
          </form>
        </div>
      </div>
    </div>
  </body>

</html>