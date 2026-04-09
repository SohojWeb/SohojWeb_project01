<?php
declare(strict_types=1);
require __DIR__ . "/includes/db.php";
session_start();

$msg = "";
$err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name === "" || $email === "" || $password === "") {
        $err = "Please fill all fields.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();
        $check->close();

        if ($exists) {
            $err = "Email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = "customer";
            $insert = $conn->prepare("INSERT INTO users(name, email, password, role, balance) VALUES (?, ?, ?, ?, 0)");
            $insert->bind_param("ssss", $name, $email, $hash, $role);
            if ($insert->execute()) {
                $msg = "Registration successful. Please login.";
            } else {
                $err = "Registration failed.";
            }
            $insert->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="/inventory/assets/style.css">
</head>
<body>
<div class="container">
  <div class="card">
    <h2>Customer Register</h2>
    <?php if ($msg): ?><div class="msg ok"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($err): ?><div class="msg err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <form method="post">
      <label>Name
        <input type="text" name="name" required>
      </label>
      <label>Email
        <input type="email" name="email" required>
      </label>
      <label>Password
        <input type="password" name="password" required>
      </label>
      <button type="submit">Register</button>
    </form>
    <p><a href="/inventory/login.php">Already have account? Login</a></p>
  </div>
</div>
</body>
</html>

