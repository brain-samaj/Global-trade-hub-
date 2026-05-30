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

// VERIFY WITH FLUTTERWAVE
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
curl_close($ch);

$response = json_decode($result, true);

// 🔒 STRICT VALIDATION
if (
    isset($response["status"]) &&
    $response["status"] === "success" &&
    $response["data"]["status"] === "successful"
) {

    $tx_ref = $response["data"]["tx_ref"];

    // CHECK ORDER EXISTS
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE reference = :ref");
    $stmt->execute([":ref" => $tx_ref]);
    $order = $stmt->fetch();

    if (!$order) {
        die("Order not found");
    }

    // MARK AS PAID ONLY ONCE
    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = 'paid'
        WHERE reference = :ref
    ");

    $stmt->execute([":ref" => $tx_ref]);

    // REDIRECT TO RECEIPT
    header("Location: receipt.php?reference=" . $tx_ref);
    exit;

} else {
    die("Payment verification failed");
}
