<?php
declare(strict_types=1);
require __DIR__ . "/../includes/db.php";
require __DIR__ . "/../includes/auth.php";
requireLogin();

if (isAdmin()) {
    header("Location: /inventory/admin/dashboard.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];
$cart = $_SESSION["cart"] ?? [];
if (count($cart) === 0) {
    header("Location: /inventory/customer/cart.php");
    exit;
}

$ids = array_keys($cart);
$idList = implode(",", array_map("intval", $ids));
$res = $conn->query("SELECT id, price FROM products WHERE id IN ($idList)");
$total = 0.0;

while ($row = $res->fetch_assoc()) {
    $pid = (int)$row["id"];
    $qty = (int)$cart[$pid];
    $total += $qty * (float)$row["price"];
}

$u = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$u->bind_param("i", $userId);
$u->execute();
$balance = (float)($u->get_result()->fetch_assoc()["balance"] ?? 0);
$u->close();

if ($balance < $total) {
    header("Location: /inventory/customer/cart.php");
    exit;
}

$conn->begin_transaction();
try {
    $deduct = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $deduct->bind_param("di", $total, $userId);
    $deduct->execute();
    $deduct->close();

    $insOrder = $conn->prepare("INSERT INTO orders(user_id, total_amount, status) VALUES(?, ?, 'paid')");
    $insOrder->bind_param("id", $userId, $total);
    $insOrder->execute();
    $orderId = $insOrder->insert_id;
    $insOrder->close();

    $insItem = $conn->prepare("INSERT INTO order_items(order_id, product_id, qty, price_at_purchase) VALUES(?, ?, ?, ?)");
    $res2 = $conn->query("SELECT id, price FROM products WHERE id IN ($idList)");
    while ($p = $res2->fetch_assoc()) {
        $pid = (int)$p["id"];
        $qty = (int)$cart[$pid];
        $price = (float)$p["price"];
        $insItem->bind_param("iiid", $orderId, $pid, $qty, $price);
        $insItem->execute();
    }
    $insItem->close();

    $conn->commit();
    $_SESSION["cart"] = [];
    header("Location: /inventory/customer/dashboard.php?checkout=success");
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    header("Location: /inventory/customer/cart.php");
    exit;
}

