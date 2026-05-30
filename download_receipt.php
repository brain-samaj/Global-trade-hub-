<?php

if (!file_exists("vendor/autoload.php")) {
    die("Composer dependencies missing");
}

require "vendor/autoload.php";
require "config/db.php";

use Dompdf\Dompdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die("Invalid reference");
}

// 🔒 ONLY PAID
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

// 🔐 QR CODE
$verifyUrl = "https://your-domain.com/verify_receipt.php?reference=" . $order['reference'];

$result = Builder::create()
    ->writer(new PngWriter())
    ->data($verifyUrl)
    ->size(180)
    ->margin(10)
    ->build();

$qr = $result->getDataUri();

$html = '
<style>
body { font-family: Arial; }

/* 🧠 LOGO */
.logo {
    position: fixed;
    top: 45%;
    left: 50%;
    width: 300px;
    opacity: 0.05;
    transform: translate(-50%, -50%);
}

/* 🔁 PATTERN */
.pattern {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    font-size: 18px;
    color: rgba(0,0,0,0.04);
    transform: rotate(-30deg);
    line-height: 100px;
    white-space: nowrap;
}
</style>

<img class="logo" src="assets/brain-logo.png">

<div class="pattern">
GLOBAL TRADE HUB • VERIFIED • '.$order['reference'].' • '.$order['customer_name'].'
</div>

<h2>Payment Receipt</h2>
<hr>

<p><b>Status:</b> PAID ✔</p>
<p><b>Product:</b> '.$order['product_name'].'</p>
<p><b>Customer:</b> '.$order['customer_name'].'</p>
<p><b>Email:</b> '.$order['email'].'</p>

<hr>

<p><b>Amount:</b> ₦'.number_format($order['amount']).'</p>
<p><b>Reference:</b> '.$order['reference'].'</p>

<hr>

<p><b>Date:</b> '.$date.'</p>
<p><b>Time:</b> '.$time.'</p>

<div style="text-align:center;margin-top:20px;">
    <p><b>Scan to verify</b></p>
    <img src="'.$qr.'" width="120">
</div>

<hr>

<p style="text-align:center;">GLOBAL TRADE HUB • VERIFIED TRANSACTION</p>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$dompdf->stream("receipt_" . $reference . ".pdf", ["Attachment" => true]);
