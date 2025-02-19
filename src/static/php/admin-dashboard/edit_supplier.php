<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../connection/db.php';

if (!isset($_GET['id'])) {
  header("Location: suppliers.php");
  exit();
}

$supplierID = $_GET['id'];

// جلب بيانات المورد
$stmt = $conn->prepare("SELECT * FROM Supplier WHERE SupplierID = :supplierID");
$stmt->execute([':supplierID' => $supplierID]);
$supplier = $stmt->fetch();

if (!$supplier) {
  die("Supplier not found.");
}

// عند إرسال النموذج، نقوم بتحديث البيانات
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $supplierName = $_POST['supplier_name'] ?? '';
  $contactPerson = $_POST['contact_person'] ?? '';
  $email = $_POST['email'] ?? '';
  $phone = $_POST['phone'] ?? '';
  $address = $_POST['address'] ?? '';

  try {
    $stmt = $conn->prepare("
            UPDATE Supplier 
            SET SupplierName = :supplierName, 
                ContactPerson = :contactPerson, 
                Email = :email, 
                Phone= :phone, 
                Address = :address 
            WHERE SupplierID = :supplierID
        ");
    $stmt->execute([
      ':supplierName' => $supplierName,
      ':contactPerson' => $contactPerson,
      ':email' => $email,
      ':phone' => $phone,
      ':address' => $address,
      ':supplierID' => $supplierID
    ]);

    // إعادة التوجيه بعد التحديث
    header("Location: suppliers.php?updated=true");
    exit();
  } catch (PDOException $e) {
    die("Error updating supplier: " . $e->getMessage());
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Supplier - Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h1 class="mb-4">Edit Supplier</h1>

    <form method="POST">
      <div class="mb-3">
        <label for="supplier_name" class="form-label">Supplier Name</label>
        <input type="text" class="form-control" id="supplier_name" name="supplier_name"
          value="<?php echo htmlspecialchars($supplier['SupplierName'] ?? ''); ?>" required>
      </div>
      <div class="mb-3">
        <label for="contact_person" class="form-label">Contact Person</label>
        <input type="text" class="form-control" id="contact_person" name="contact_person"
          value="<?php echo htmlspecialchars($supplier['ContactPerson'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email"
          value="<?php echo htmlspecialchars($supplier['Email'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label for="phone" class="form-label">Phone</label>
        <input type="text" class="form-control" id="phone" name="phone"
          value="<?php echo htmlspecialchars($supplier['PhoneNumber'] ?? ''); ?>">
      </div>
      <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <textarea class="form-control" id="address" name="address"
          rows="3"><?php echo htmlspecialchars($supplier['Address'] ?? ''); ?></textarea>
      </div>
      <button type="submit" name="edit_supplier" class="btn btn-primary">Update Supplier</button>
    </form>
  </div>

  <script src="../../js/admin-dashboard.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>