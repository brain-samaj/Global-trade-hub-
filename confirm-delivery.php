<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["user_id"])) {
    exit("Login required");
}

$order_id = $_GET["id"] ?? 0;

$stmt = $pdo->prepare("
SELECT *
FROM orders
WHERE id=?
LIMIT 1
");

$stmt->execute([$order_id]);

$order = $stmt->fetch();

if (!$order) {
    exit("Order not found");
}

if ($order["buyer_confirmed_delivery"]) {
    exit("Already confirmed");
}

$stmt = $pdo->prepare("
UPDATE orders
SET
status='completed',
buyer_confirmed_delivery=TRUE
WHERE id=?
");

$stmt->execute([$order_id]);

$stmt = $pdo->prepare("
UPDATE sellers
SET wallet_balance =
wallet_balance + ?
WHERE id = ?
");

$stmt->execute([
    $order["seller_earnings"],
    $order["seller_id"]
]);

echo "Delivery confirmed successfully.";
?>
