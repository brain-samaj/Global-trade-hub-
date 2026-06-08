<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

$id = $_GET["id"] ?? null;

if (!$id) {
    exit("Invalid product ID");
}

/*
|------------------------------------------------
| CHECK PRODUCT OWNER
|------------------------------------------------
*/

$stmt = $pdo->prepare("SELECT seller_id FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    exit("Product not found");
}

/*
|------------------------------------------------
| PERMISSION CHECK
|------------------------------------------------
| Admin = full access
| Seller = only own product
*/

if ($role !== "admin" && $product["seller_id"] != $user_id) {
    exit("Access denied");
}

/*
|------------------------------------------------
| DELETE PRODUCT
|------------------------------------------------
*/

$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

/*
|------------------------------------------------
| REDIRECT BASED ON ROLE
|------------------------------------------------
*/

if ($role === "admin") {
    header("Location: admin/dashboard.php?deleted=1");
} else {
    header("Location: seller-dashboard.php?deleted=1");
}

exit();
?>
