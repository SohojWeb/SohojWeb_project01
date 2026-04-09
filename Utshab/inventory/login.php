<?php
declare(strict_types=1);
require __DIR__ . "/includes/db.php";
session_start();

$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = (int)$user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["cart"] = $_SESSION["cart"] ?? [];

        if ($user["role"] === "admin") {
            header("Location: /inventory/admin/dashboard.php");
        } else {
            header("Location: /inventory/customer/dashboard.php");
        }
        exit;
    }
    $err = "Invalid credentials.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="/inventory/assets/style.css">
</head>
<body>
<div class="container">
  <div class="card">
    <h2>Login</h2>
    <?php if ($err): ?><div class="msg err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <form method="post">
      <label>Email
        <input type="email" name="email" required>
      </label>
      <label>Password
        <input type="password" name="password" required>
      </label>
      <button type="submit">Login</button>
    </form>
    <p><a href="/inventory/register.php">New customer? Register</a></p>
  </div>
</div>
</body>
</html>

