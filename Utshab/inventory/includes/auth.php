<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn(): bool
{
    return isset($_SESSION["user_id"]);
}

function isAdmin(): bool
{
    return isset($_SESSION["role"]) && $_SESSION["role"] === "admin";
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header("Location: /inventory/login.php");
        exit;
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        header("Location: /inventory/customer/dashboard.php");
        exit;
    }
}

