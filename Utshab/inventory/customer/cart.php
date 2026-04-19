<?php
declare(strict_types=1);
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
requireLogin();

if (isAdmin()) {
    header("Location: /inventory/admin/dashboard.php");
    exit;
}

$cart = $_SESSION["cart"] ?? [];
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (($_POST["action"] ?? "") === "remove_item") {
        $pid = (int)($_POST["product_id"] ?? 0);
        unset($cart[$pid]);
        $_SESSION["cart"] = $cart;
        $msg = "Item removed.";
    }
}

$ids = array_keys($cart);
$items = [];
$total = 0.0;

if (count($ids) > 0) {
    $idList = implode(",", array_map("intval", $ids));
    $res = $conn->query("SELECT id, name, price FROM products WHERE id IN ($idList)");
    while ($row = $res->fetch_assoc()) {
        $qty = (int)$cart[(int)$row["id"]];
        $sub = $qty * (float)$row["price"];
        $total += $sub;
        $items[] = [
            "id" => (int)$row["id"],
            "name" => $row["name"],
            "price" => (float)$row["price"],
            "qty" => $qty,
            "sub" => $sub
        ];
    }
}

$u = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$u->bind_param("i", $_SESSION["user_id"]);
$u->execute();
$balance = (float)($u->get_result()->fetch_assoc()["balance"] ?? 0);
$u->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart</title>
  <link rel="stylesheet" href="/inventory/assets/style.css">
  <script src="/inventory/assets/app.js"></script>
</head>
<body>
<div class="container">
  <div class="card">
    <h2>Cart</h2>
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
          <th>Qty</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php if (count($items) === 0): ?>
        <tr><td colspan="5">Cart is empty.</td></tr>
      <?php else: ?>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item["name"]) ?></td>
            <td><?= number_format($item["price"], 2) ?></td>
            <td><?= $item["qty"] ?></td>
            <td><?= number_format($item["sub"], 2) ?></td>
            <td>
              <form method="post">
                <input type="hidden" name="action" value="remove_item">
                <input type="hidden" name="product_id" value="<?= $item["id"] ?>">
                <button type="submit" class="secondary">Remove</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
    <h3 class="right">Total: <?= number_format($total, 2) ?></h3>
    <p><strong>Your Balance:</strong> <?= number_format($balance, 2) ?></p>
    <?php if ($total > 0): ?>
      <form method="post" action="/inventory/customer/checkout.php" onsubmit="return confirmCheckout('<?= number_format($total, 2, '.', '') ?>', '<?= number_format($balance, 2, '.', '') ?>')">
        <button type="submit">Checkout</button>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

