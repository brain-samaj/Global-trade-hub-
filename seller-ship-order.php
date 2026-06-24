<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$order_id = (int)($_GET["id"] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$order_id]);

$order = $stmt->fetch();

if (!$order) {
    die("Order not found");
}

$stmt = $pdo->prepare("
    UPDATE orders
    SET
        seller_confirmed_shipped = TRUE,
        shipped_at = NOW(),
        status = 'shipped'
    WHERE id = ?
");
$stmt->execute([$order_id]);

if (!empty($order["user_id"])) {

    $stmt = $pdo->prepare("
        INSERT INTO notifications
        (user_id, message)
        VALUES (?, ?)
    ");

    $stmt->execute([
        $order["user_id"],
        "Your order #{$order_id} has been shipped by the seller."
    ]);
}

header("Location: seller-dashboard.php");
exit();
?>
