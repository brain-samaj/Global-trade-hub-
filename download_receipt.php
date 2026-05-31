<?php

if (!file_exists("vendor/autoload.php")) {
    die("Composer dependencies missing");
}

require "vendor/autoload.php";
require "config/db.php";

use Dompdf\Dompdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

/*
|--------------------------------------------------------------------------
| GET ORDER
|--------------------------------------------------------------------------
*/

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die("Invalid reference");
}

/*
|--------------------------------------------------------------------------
| FETCH ONLY PAID ORDER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT o.*, p.name AS product_name
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.reference = :ref
    AND o.status = 'paid'
");

$stmt->execute([":ref" => $reference]);
$order = $stmt->fetch();

if (!$order) {
    die("Unauthorized access");
}

/*
|--------------------------------------------------------------------------
| DATE / TIME FIX
|--------------------------------------------------------------------------
*/

$date = date("Y-m-d", strtotime($order["created_at"]));
$time = date("H:i:s", strtotime($order["created_at"]));

/*
|--------------------------------------------------------------------------
| SECURITY SIGNATURE (IMPORTANT)
|--------------------------------------------------------------------------
*/

$secret = getenv("APP_SECRET");

$sig = hash_hmac('sha256', $order["reference"], $secret);

/*
|--------------------------------------------------------------------------
| VERIFY LINK (SECURE)
|--------------------------------------------------------------------------
*/

$verifyLink =
    "https://global-trade-hub-3nbz.onrender.com/verify_receipt.php?reference="
    . urlencode($order["reference"])
    . "&sig=" . $sig;

/*
|--------------------------------------------------------------------------
| QR CODE GENERATION
|--------------------------------------------------------------------------
*/

$result = Builder::create()
    ->writer(new PngWriter())
    ->data($verifyLink)
    ->size(180)
    ->margin(10)
    ->build();

$qr = $result->getDataUri();

/*
|--------------------------------------------------------------------------
| HTML RECEIPT
|--------------------------------------------------------------------------
*/

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<style>

body{
    font-family: Arial, sans-serif;
    margin:40px;
}

.pattern{
    position:fixed;
    top:45%;
    left:10%;
    font-size:24px;
    opacity:0.04;
    transform:rotate(-30deg);
}

.footer{
    text-align:center;
    margin-top:30px;
    font-size:12px;
}

hr{
    margin:15px 0;
}

</style>

</head>

<body>

<div class="pattern">
GLOBAL TRADE HUB • VERIFIED TRANSACTION
</div>

<h2>Payment Receipt</h2>

<hr>

<p><strong>Status:</strong> PAID ✔</p>

<p><strong>Product:</strong> ' . htmlspecialchars($order["product_name"]) . '</p>

<p><strong>Customer:</strong> ' . htmlspecialchars($order["customer_name"]) . '</p>

<p><strong>Email:</strong> ' . htmlspecialchars($order["email"]) . '</p>

<hr>

<p><strong>Amount Paid:</strong> ₦' . number_format($order["amount"]) . '</p>

<p><strong>Reference:</strong> ' . htmlspecialchars($order["reference"]) . '</p>

<hr>

<p><strong>Date:</strong> ' . $date . '</p>

<p><strong>Time:</strong> ' . $time . '</p>

<hr>

<div style="text-align:center;margin-top:20px;">
    <p><strong>Scan to verify receipt</strong></p>
    <img src="' . $qr . '" width="140">
</div>

<div class="footer">
GLOBAL TRADE HUB<br>
Verified Payment Receipt
</div>

</body>
</html>
';

/*
|--------------------------------------------------------------------------
| GENERATE PDF
|--------------------------------------------------------------------------
*/

$dompdf = new Dompdf();

$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$dompdf->stream(
    "receipt_" . $reference . ".pdf",
    ["Attachment" => true]
);

exit;
