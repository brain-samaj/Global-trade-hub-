<?php
require "config/db.php";
require "config/paystack.php";

if (!isset($_GET['reference'])) {
    die("No reference supplied");
}

$reference = $_GET['reference'];

$url = "https://api.paystack.co/transaction/verify/" . rawurlencode($reference);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $paystack_secret"
]);

$result = curl_exec($ch);
curl_close($ch);

$response = json_decode($result, true);

if ($response["data"]["status"] === "success") {

    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = 'paid'
        WHERE reference = :ref
    ");

    $stmt->execute([":ref" => $reference]);

    echo "Payment successful! Order confirmed.";
} else {
    echo "Payment failed or pending.";
}
