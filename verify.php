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

// Verify transaction with Flutterwave
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
    die("Curl Error: " . curl_error($ch));
}

curl_close($ch);

$response = json_decode($result, true);

if (
    isset($response["status"]) &&
    $response["status"] === "success" &&
    isset($response["data"]["status"]) &&
    $response["data"]["status"] === "successful"
) {

    $reference = $response["data"]["tx_ref"] ?? null;

    if ($reference) {

        $stmt = $pdo->prepare("
            UPDATE orders
            SET status = 'paid'
            WHERE reference = :reference
        ");

        $stmt->execute([
            ":reference" => $reference
        ]);
    }

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Payment Successful</title>
    </head>
    <body>
        <h1>Payment Successful ✅</h1>
        <p>Thank you for your payment.</p>
        <p>Transaction ID: <?php echo htmlspecialchars($transaction_id); ?></p>
        <p>Reference: <?php echo htmlspecialchars($reference ?? "N/A"); ?></p>

        <a href="index.php">Return Home</a>
    </body>
    </html>
    <?php

} else {

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Payment Failed</title>
    </head>
    <body>
        <h1>Payment Failed ❌</h1>
        <p>Your payment could not be verified.</p>

        <a href="index.php">Return Home</a>
    </body>
    </html>
    <?php
}
?>
