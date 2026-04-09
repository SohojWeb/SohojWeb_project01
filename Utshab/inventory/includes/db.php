<?php
declare(strict_types=1);

$host = "localhost";
$user = "root";
$pass = "";
$dbName = "shop_inventory";

$conn = new mysqli($host, $user, $pass, $dbName);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

