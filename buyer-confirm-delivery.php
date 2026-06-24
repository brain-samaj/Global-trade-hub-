<?php
session_start();

require "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$order_id = (int)($_GET["id"] ?? 0);

if (!$order_id) {
    die("Invalid order");
}

/*
|--------------------------------------------------------------------------
| GET ORDER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found");
}

if ($order["buyer_confirmed_delivery"]) {
    die("Already confirmed");
}

if (!$order["seller_confirmed_shipped"]) {
    die("Seller has not marked this order as shipped yet.");
}

/*
|--------------------------------------------------------------------------
| MARK DELIVERED
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    UPDATE orders
    SET
        status = 'completed',
        buyer_confirmed_delivery = TRUE,
        delivered_at = NOW()
    WHERE id = ?
");

$stmt->execute([$order_id]);

/*
|--------------------------------------------------------------------------
| RELEASE MONEY TO SELLER WALLET
|--------------------------------------------------------------------------
*/

if (!$order["wallet_released"]) {

    $stmt = $pdo->prepare("
        UPDATE sellers
        SET wallet_balance = wallet_balance + ?
        WHERE user_id = ?
    ");

    $stmt->execute([
        $order["seller_earnings"],
        $order["seller_id"]
    ]);

    $stmt = $pdo->prepare("
        UPDATE orders
        SET wallet_released = TRUE
        WHERE id = ?
    ");

    $stmt->execute([$order_id]);
}

/*
|--------------------------------------------------------------------------
| NOTIFY SELLER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    INSERT INTO notifications
    (user_id, message)
    VALUES (?, ?)
");

$stmt->execute([
    $order["seller_id"],
    "Buyer has confirmed delivery. Funds have been released to your wallet."
]);

header("Location: orders.php");
exit();
?>
