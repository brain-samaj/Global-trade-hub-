<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "seller") {
    header("Location: login.php");
    exit();
}

$id = $_GET["id"] ?? null;
if (!$id) die("Invalid ID");

$stmt = $pdo->prepare("
    DELETE FROM products
    WHERE id = :id AND seller_id = :sid
");

$stmt->execute([
    ":id" => $id,
    ":sid" => $_SESSION["user_id"]
]);

header("Location: seller-products.php");
exit();
