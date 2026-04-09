<?php
declare(strict_types=1);
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
requireLogin();

if (isAdmin()) {
    header("Location: /inventory/admin/dashboard.php");
    exit;
}

$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "add_to_cart") {
    $productId = (int)($_POST["product_id"] ?? 0);
    $qty = (int)($_POST["qty"] ?? 1);
    if ($productId > 0 && $qty > 0) {
        if (!isset($_SESSION["cart"][$productId])) {
            $_SESSION["cart"][$productId] = 0;
        }
        $_SESSION["cart"][$productId] += $qty;
        $msg = "Added to cart.";
    }
}

$products = $conn->query("SELECT id, name, price FROM products ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products</title>
  <link rel="stylesheet" href="/inventory/assets/style.css">
</head>
<body>
<div class="container">
  <div class="card">
    <h2>Products</h2>
    <div class="nav">
      <a href="/inventory/customer/dashboard.php">Dashboard</a>
      <a href="/inventory/customer/products.php">Products</a>
      <a href="/inventory/customer/cart.php">Cart</a>
      <a href="/inventory/logout.php" class="secondary">Logout</a>
    </div>
    <?php if ($msg): ?><div class="msg ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Add</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($p = $products->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($p["name"]) ?></td>
          <td><?= number_format((float)$p["price"], 2) ?></td>
          <td>
            <form method="post" style="display:flex; gap:8px;">
              <input type="hidden" name="action" value="add_to_cart">
              <input type="hidden" name="product_id" value="<?= (int)$p["id"] ?>">
              <input type="number" name="qty" min="1" value="1" style="width:90px;">
              <button type="submit">Add</button>
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

