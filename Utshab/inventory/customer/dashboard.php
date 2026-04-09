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
$err = "";
$userId = (int)$_SESSION["user_id"];

if (($_GET["checkout"] ?? "") === "success") {
    $msg = "Purchase successful. Amount deducted from main balance.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && ($_POST["action"] ?? "") === "add_balance") {
    $amount = (float)($_POST["amount"] ?? 0);
    if ($amount <= 0) {
        $err = "Enter valid amount.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $stmt->bind_param("di", $amount, $userId);
        $stmt->execute();
        $stmt->close();
        $msg = "Balance added successfully (from other app).";
    }
}

$userStmt = $conn->prepare("SELECT name, email, balance FROM users WHERE id = ?");
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();
$userStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard</title>
  <link rel="stylesheet" href="/inventory/assets/style.css">
</head>
<body>
<div class="container">
  <div class="card">
    <h2>Customer Dashboard</h2>
    <div class="nav">
      <a href="/inventory/customer/dashboard.php">Dashboard</a>
      <a href="/inventory/customer/products.php">View Products</a>
      <a href="/inventory/customer/cart.php">Cart</a>
      <a href="/inventory/logout.php" class="secondary">Logout</a>
    </div>
    <?php if ($msg): ?><div class="msg ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="msg err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <p><strong>Name:</strong> <?= htmlspecialchars($user["name"] ?? "") ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user["email"] ?? "") ?></p>
    <p><strong>Main Balance:</strong> <?= number_format((float)($user["balance"] ?? 0), 2) ?></p>
  </div>

  <div class="card">
    <h3>Add Balance (from other app)</h3>
    <form method="post">
      <input type="hidden" name="action" value="add_balance">
      <label>Amount
        <input type="number" step="0.01" name="amount" required>
      </label>
      <button type="submit">Add Balance</button>
    </form>
  </div>
</div>
</body>
</html>

