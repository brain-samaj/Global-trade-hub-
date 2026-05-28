<?php

require "config/db.php";

$paystack_secret = getenv("PAYSTACK_SECRET_KEY");

if (!isset($_GET['reference'])) {
    die("No payment reference found");
}

$reference = $_GET['reference'];

// ============================
// VERIFY PAYMENT WITH PAYSTACK
// ============================

$url = "https://api.paystack.co/transaction/verify/" . urlencode($reference);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $paystack_secret
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// ============================
// CHECK PAYMENT STATUS
// ============================

if (
    isset($result['status']) &&
    $result['status'] === true &&
    $result['data']['status'] === 'success'
) {

    // Update order to PAID
    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = 'paid'
        WHERE reference = :reference
    ");

    $stmt->execute([
        ":reference" => $reference
    ]);

    echo "<h2>Payment Successful ✅</h2>";
    echo "<p>Your order has been confirmed.</p>";

} else {

    // Mark failed
    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = 'failed'
        WHERE reference = :reference
    ");

    $stmt->execute([
        ":reference" => $reference
    ]);

    echo "<h2>Payment Failed ❌</h2>";
    echo "<p>Please try again.</p>";
}
