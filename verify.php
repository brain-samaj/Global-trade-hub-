<?php

require "config/db.php";

$flutterwave_secret = getenv("FLUTTERWAVE_SECRET_KEY");

if (!$flutterwave_secret) {
    die("Flutterwave secret key not set");
}

if (!isset($_GET['transaction_id'])) {
    die("No transaction ID supplied");
}

$transaction_id = $_GET['transaction_id'];

// VERIFY TRANSACTION WITH FLUTTERWAVE
$url = "https://api.flutterwave.com/v3/transactions/" . $transaction_id . "/verify";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $flutterwave_secret,
        "Content-Type: application/json"
    ]
]);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    die("Verification failed: " . curl_error($ch));
}

curl_close($ch);

$response = json_decode($result, true);

if (
    !isset($response["status"]) ||
    $response["status"] !== "success" ||
    !isset($response["data"])
) {
    die("Invalid verification response");
}

$data = $response["data"];

if (
    !isset($data["status"]) ||
    $data["status"] !== "successful"
) {
    die("Payment was not successful");
}

$tx_ref = $data["tx_ref"] ?? null;

if (!$tx_ref) {
    die("Missing transaction reference");
}

// FIND ORDER
$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE reference = :ref
    LIMIT 1
");

$stmt->execute([
    ":ref" => $tx_ref
]);

// GET PRODUCT INFO
$productStmt = $pdo->prepare("
    SELECT p.*, u.id AS seller_user_id
    FROM products p
    JOIN users u ON p.seller_id = u.id
    WHERE p.id = :pid
");

$productStmt->execute([
    ":pid" => $order["product_id"]
]);

$product = $productStmt->fetch();

if ($product) {

    $notify = $pdo->prepare("
        INSERT INTO notifications (
            user_id,
            message
        )
        VALUES (
            :uid,
            :msg
        )
    ");

    $notify->execute([
        ":uid" => $product["seller_user_id"],
        ":msg" =>
            "New order received. Order Ref: " .
            $tx_ref
    ]);
}

$order = $stmt->fetch();

if (!$order) {
    die("Order not found");
}

// EXTRA SECURITY CHECKS

$paidAmount = (float)($data["amount"] ?? 0);
$orderAmount = (float)$order["amount"];

if ($paidAmount < $orderAmount) {
    die("Amount mismatch");
}

if (
    isset($data["currency"]) &&
    $data["currency"] !== "NGN"
) {
    die("Invalid currency");
}

// ALREADY PAID?
if ($order["status"] !== "paid") {

    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = 'paid'
        WHERE reference = :ref
    ");

$stmt = $pdo->prepare("
INSERT INTO notifications
(user_id,message)
VALUES
(:user_id,:message)
");

$stmt->execute([
":user_id"=>$order["seller_id"],
":message"=>"New order received from ".$order["customer_name"].". Phone: ".$order["phone"]
]);

    $stmt->execute([
        ":ref" => $tx_ref
    ]);
}

// REDIRECT TO RECEIPT
header(
    "Location: receipt.php?reference=" .
    urlencode($tx_ref)
);

exit;
?>
