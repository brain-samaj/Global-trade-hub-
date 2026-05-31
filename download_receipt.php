<?php

if (!file_exists("vendor/autoload.php")) {
    die("Composer dependencies missing");
}

require "vendor/autoload.php";
require "config/db.php";

use Dompdf\Dompdf;

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die("Invalid reference");
}

// 🔒 ONLY PAID ORDERS
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

$date = date("Y-m-d", strtotime($order["created_at"] ?? "now"));
$time = date("H:i:s", strtotime($order["created_at"] ?? "now"));

$verifyLink =
    "https://global-trade-hub-3nbz.onrender.com/verify_receipt.php?reference="
    . urlencode($order["reference"]);

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

.logo{
    position:fixed;
    top:35%;
    left:20%;
    width:400px;
    opacity:0.05;
}

.pattern{
    position:fixed;
    top:45%;
    left:10%;
    font-size:24px;
    opacity:0.04;
    transform:rotate(-30deg);
}

hr{
    margin:15px 0;
}

.footer{
    text-align:center;
    margin-top:30px;
    font-size:12px;
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

<p><strong>Product:</strong> '
. htmlspecialchars($order["product_name"]) .
'</p>

<p><strong>Customer:</strong> '
. htmlspecialchars($order["customer_name"]) .
'</p>

<p><strong>Email:</strong> '
. htmlspecialchars($order["email"]) .
'</p>

<hr>

<p><strong>Amount Paid:</strong> ₦'
. number_format($order["amount"]) .
'</p>

<p><strong>Reference:</strong> '
. htmlspecialchars($order["reference"]) .
'</p>

<hr>

<p><strong>Date:</strong> '
. $date .
'</p>

<p><strong>Time:</strong> '
. $time .
'</p>

<hr>

<p>
<strong>Verify Receipt:</strong><br>
' . $verifyLink . '
</p>

<div class="footer">
GLOBAL TRADE HUB<br>
Verified Payment Receipt
</div>

</body>
</html>
';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper("A4", "portrait");

$dompdf->render();

$dompdf->stream(
    "receipt_" . $reference . ".pdf",
    ["Attachment" => true]
);

exit;
