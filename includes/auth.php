<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
|--------------------------------------------------------------------------
| BASIC LOGIN CHECK
|--------------------------------------------------------------------------
*/
function requireLogin()
{
    if (!isset($_SESSION["user_id"])) {
        header("Location: /login.php");
        exit();
    }
}

/*
|--------------------------------------------------------------------------
| ADMIN ONLY
|--------------------------------------------------------------------------
*/
function checkAdmin()
{
    requireLogin();

    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
        exit("Access denied: Admins only.");
    }
}

/*
|--------------------------------------------------------------------------
| SELLER ONLY
|--------------------------------------------------------------------------
*/
function checkSeller()
{
    requireLogin();

    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "seller") {
        exit("Access denied: Sellers only.");
    }
}

/*
|--------------------------------------------------------------------------
| BUYER ONLY
|--------------------------------------------------------------------------
*/
function checkBuyer()
{
    requireLogin();

    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "buyer") {
        exit("Access denied: Buyers only.");
    }
}
