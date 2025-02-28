<?php
require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
    header("Location: menu_items.php");
    exit();
}

$itemId = $_GET['id'];

// Fetch item details
$stmt = $conn->prepare("SELECT * FROM MenuItem WHERE ItemID = :id");
$stmt->execute([':id' => $itemId]);
$item = $stmt->fetch();

if (!$item) {
    header("Location: menu_items.php");
    exit();
}

// Fetch all categories
$stmt = $conn->query("SELECT * FROM MenuCategory");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $_POST['itemName'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $categoryID = $_POST['categoryID'];
    $availability = isset($_POST['availability']) ? 1 : 0;

    // Handle image upload
    $imageURL = $item['ImageURL']; // Keep the existing image URL by default
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../../uploads/Menu-item/"; // Ensure this directory exists and is writable
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Check file size (5MB max)
            if ($_FILES["image"]["size"] <= 5000000) {
                // Allow certain file formats
                if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg" || $imageFileType == "gif") {
                    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                        $imageURL = "../../uploads/Menu-item/" . basename($_FILES["image"]["name"]);
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

    // Update the menu item in the database
    $stmt = $conn->prepare("
        UPDATE MenuItem 
        SET ItemName = :itemName, Description = :description, Price = :price, ImageURL = :imageURL, CategoryID = :categoryID, Availability = :availability
        WHERE ItemID = :id
    ");
    $stmt->execute([
        ':itemName' => $itemName,
        ':description' => $description,
        ':price' => $price,
        ':imageURL' => $imageURL,
        ':categoryID' => $categoryID,
        ':availability' => $availability,
        ':id' => $itemId
    ]);

    header("Location: menu_items.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item - ELCHEF</title>
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
                <li class="active"><a href="menu_items.php"><i class="fas fa-utensils"></i> Menu Items</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
                <li><a href="inventory.php"><i class="fas fa-box"></i> Inventory</a></li>
                <li><a href="suppliers.php"><i class="fa-solid fa-truck"></i></i> Suppliers</a></li>
                <li><a href="admin_notifications.php"><i class="fa-solid fa-bell"></i> Notifications</a></li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <button type="button" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div id="content">
                <div class="header"></div>
                <div class="main-content">
                    <h2>Edit Menu Item</h2>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="itemName" class="form-label">Item Name</label>
                            <input type="text" class="form-control" id="itemName" name="itemName"
                                value="<?php echo $item['ItemName']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description"
                                rows="3"><?php echo $item['Description']; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price"
                                value="<?php echo $item['Price']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoryID" class="form-label">Category</label>
                            <select class="form-control" id="categoryID" name="categoryID" required>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['CategoryID']; ?>" <?php echo $category['CategoryID'] == $item['CategoryID'] ? 'selected' : ''; ?>>
                                        <?php echo $category['CategoryName']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <?php if ($item['ImageURL']): ?>
                                <p>Current Image: <img
                                        src="../../../../src/static/uploads/Menu-item/?php echo $item['ImageURL']; ?>"
                                        alt="Current Image" style="max-width: 200px;"></p>
                            <?php endif; ?>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="availability" name="availability" <?php echo $item['Availability'] ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="availability">Available</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Item</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="../../js/admin-dashboard.js"></script>
</body>

</html>