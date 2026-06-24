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

/*
|--------------------------------------------------------------------------
| VERIFY TRANSACTION
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| FIND ORDER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE reference = :ref
    LIMIT 1
");

$stmt->execute([
    ":ref" => $tx_ref
]);

$order = $stmt->fetch();

if (!$order) {
    die("Order not found");
}

/*
|--------------------------------------------------------------------------
| SECURITY CHECKS
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| UPDATE ORDER TO PAID
|--------------------------------------------------------------------------
*/

if ($order["status"] !== "paid") {

    $updateOrder = $pdo->prepare("
        UPDATE orders
        SET status = 'paid'
        WHERE reference = :ref
    ");

    $updateOrder->execute([
        ":ref" => $tx_ref
    ]);

    /*
    |--------------------------------------------------------------------------
    | NOTIFY SELLER
    |--------------------------------------------------------------------------
    */

    $notifySeller = $pdo->prepare("
        INSERT INTO notifications
        (user_id, message)
        VALUES
        (:user_id, :message)
    ");

    $notifySeller->execute([
        ":user_id" => $order["seller_id"],
        ":message" =>
            "New order received from " .
            $order["customer_name"] .
            ". Phone: " .
            $order["phone"]
    ]);
}

/*
|--------------------------------------------------------------------------
| REDIRECT TO RECEIPT
|--------------------------------------------------------------------------
*/

header(
    "Location: receipt.php?reference=" .
    urlencode($tx_ref)
);

exit;
?>
