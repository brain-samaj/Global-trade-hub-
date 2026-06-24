<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "seller") {
    die("Access denied");
}

$seller_id = $_SESSION["user_id"];

$order_id = (int)($_GET["id"] ?? 0);

if (!$order_id) {
    die("Invalid order");
}

/*
|--------------------------------------------------------------------------
| VERIFY ORDER BELONGS TO SELLER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT o.*
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.id = ?
    AND p.seller_id = ?
    LIMIT 1
");

$stmt->execute([
    $order_id,
    $seller_id
]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found or access denied");
}

/*
|--------------------------------------------------------------------------
| ALREADY SHIPPED?
|--------------------------------------------------------------------------
*/

if ($order["seller_confirmed_shipped"]) {
    header("Location: seller-dashboard.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| MARK AS SHIPPED
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    UPDATE orders
    SET
        seller_confirmed_shipped = TRUE,
        shipped_at = NOW(),
        status = 'shipped'
    WHERE id = ?
");

$stmt->execute([$order_id]);

/*
|--------------------------------------------------------------------------
| NOTIFY BUYER
|--------------------------------------------------------------------------
*/

if (!empty($order["user_id"])) {

    $stmt = $pdo->prepare("
        INSERT INTO notifications
        (user_id, message)
        VALUES (?, ?)
    ");

    $stmt->execute([
        $order["user_id"],
        "Your order has been shipped by the seller. Please confirm delivery once received."
    ]);
}

header("Location: seller-dashboard.php");
exit();
?>
