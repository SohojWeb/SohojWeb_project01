<?php
session_start();
if (isset($_SESSION["user_id"])) {
    if (($_SESSION["role"] ?? "") === "admin") {
        header("Location: /inventory/admin/dashboard.php");
        exit;
    }
    header("Location: /inventory/customer/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shop Inventory App</title>
  <link rel="stylesheet" href="/inventory/assets/style.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>Shop Inventory App</h1>
      <p>Raw PHP app with separate admin and customer dashboard.</p>
      <div class="nav">
        <a href="/inventory/login.php">Login</a>
        <a href="/inventory/register.php" class="secondary">Register Customer</a>
      </div>
    </div>
  </div>
</body>
</html>

