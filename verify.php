<?php
require "config/db.php";
require "config/paystack.php";

if (!isset($_GET['reference'])) {
    die("No reference supplied");
}

$reference = $_GET['reference'];

// Paystack verify URL (IMPORTANT FIX)
$url = "https://api.paystack.co/transaction/verify/" . urlencode($reference);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $paystack_secret
]);

$result = curl_exec($ch);

if (curl_errno($ch)) {
    die("Curl Error: " . curl_error($ch));
}

curl_close($ch);

$response = json_decode($result, true);

// Safe validation
if (
    isset($response["status"]) &&
    $response["status"] === true &&
    isset($response["data"]["status"]) &&
    $response["data"]["status"] === "success"
) {

    // Update order to paid
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
?>
