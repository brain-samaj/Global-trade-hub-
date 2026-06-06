<?php
require "config/db.php";
$flutterwave_secret = getenv("FLUTTERWAVE_SECRET_KEY");
$flutterwave_public = getenv("FLUTTERWAVE_PUBLIC_KEY");
$status = $_GET['status'] ?? null;
$tx_ref = $_GET['tx_ref'] ?? null;

if (!$tx_ref) {
    die("Invalid request");
}

// verify via Flutterwave API
$url = "https://api.flutterwave.com/v3/transactions/verify_by_reference?tx_ref=" . $tx_ref;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $flutterwave_secret"
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (
    isset($data["status"]) &&
    $data["status"] === "success" &&
    $data["data"]["status"] === "successful"
) {

    $stmt = $pdo->prepare("
        UPDATE seller_payments
        SET status = 'paid'
        WHERE reference = :ref
    ");

    $stmt->execute([":ref" => $tx_ref]);

    header("Location: seller-register.php?ref=$tx_ref");
    exit;

} else {
    echo "Payment verification failed";
}
