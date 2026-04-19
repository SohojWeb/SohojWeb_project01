<?php
declare(strict_types=1);
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
requireAdmin();

$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    if ($_POST["action"] === "add_product") {
        $name = trim($_POST["name"] ?? "");
        $price = (float)($_POST["price"] ?? 0);
        if ($name === "" || $price <= 0) {
            $err = "Enter valid product name and price.";
        } else {
            $stmt = $conn->prepare("INSERT INTO products(name, price) VALUES(?, ?)");
            $stmt->bind_param("sd", $name, $price);
            $stmt->execute();
            $stmt->close();
            $msg = "Product added.";
        }
    }

    if ($_POST["action"] === "update_price") {
        $productId = (int)($_POST["product_id"] ?? 0);
        $price = (float)($_POST["new_price"] ?? 0);
        if ($productId <= 0 || $price <= 0) {
            $err = "Enter valid new price.";
        } else {
            $stmt = $conn->prepare("UPDATE products SET price = ? WHERE id = ?");
            $stmt->bind_param("di", $price, $productId);
            $stmt->execute();
            $stmt->close();
            $msg = "Price updated successfully.";
        }
    }
}

$products = $conn->query("SELECT id, name, price FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/inventory/assets/style.css">
</head>
<body>
<div class="container">
  <div class="card">
    <h2>Admin Dashboard</h2>
    <div class="nav">
      <a href="/inventory/admin/dashboard.php">Products</a>
      <a href="/inventory/logout.php" class="secondary">Logout</a>
    </div>
    <?php if ($msg): ?><div class="msg ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="msg err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <div class="grid-2">
      <form method="post" class="card">
        <h3>Add Product</h3>
        <input type="hidden" name="action" value="add_product">
        <label>Product Name
          <input type="text" name="name" required>
        </label>
        <label>Price
          <input type="number" step="0.01" name="price" required>
        </label>
        <button type="submit">Add Product</button>
      </form>
      <div class="card">
        <h3>Note</h3>
        <p>Only admin can set and change product price.</p>
        <p>Create default admin from SQL file first.</p>
      </div>
    </div>
  </div>

  <div class="card">
    <h3>All Products</h3>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Price</th>
          <th>Change Price</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $products->fetch_assoc()): ?>
        <tr>
          <td><?= (int)$row["id"] ?></td>
          <td><?= htmlspecialchars($row["name"]) ?></td>
          <td><?= number_format((float)$row["price"], 2) ?></td>
          <td>
            <form method="post" style="display:flex; gap:8px;">
              <input type="hidden" name="action" value="update_price">
              <input type="hidden" name="product_id" value="<?= (int)$row["id"] ?>">
              <input type="number" step="0.01" name="new_price" placeholder="New price" required>
              <button type="submit">Update</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>

