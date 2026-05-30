<?php

require "config/db.php";
require "vendor/autoload.php";

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die("Invalid receipt reference");
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
    die("Invalid or unpaid transaction");
}

// DATE / TIME
$date = date("Y-m-d", strtotime($order["created_at"] ?? "now"));
$time = date("H:i:s", strtotime($order["created_at"] ?? "now"));

// 🔐 QR VERIFY LINK
$verifyUrl = "https://your-domain.com/verify_receipt.php?reference=" . $order['reference'];

$result = Builder::create()
    ->writer(new PngWriter())
    ->data($verifyUrl)
    ->size(220)
    ->margin(10)
    ->build();

$qrDataUri = $result->getDataUri();

?>

<!DOCTYPE html>
<html>
<head>
<title>Receipt</title>

<style>
body {
    font-family: Arial;
    background: #f5f5f5;
}

/* 🧠 LOGO WATERMARK */
.wm-logo {
    position: fixed;
    top: 50%;
    left: 50%;
    width: 380px;
    transform: translate(-50%, -50%);
    opacity: 0.05;
    z-index: 0;
    pointer-events: none;
}

/* 🔁 DIAGONAL PATTERN */
.wm-text {
    position: fixed;
    top: 0;
    left: 0;
    width: 200%;
    height: 200%;
    font-size: 20px;
    color: rgba(0,0,0,0.04);
    transform: rotate(-30deg);
    white-space: nowrap;
    line-height: 120px;
    z-index: 0;
    pointer-events: none;
}

/* 📄 CONTENT */
.receipt-box {
    position: relative;
    z-index: 2;
    max-width: 650px;
    margin: 40px auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
}

.status {
    color: green;
    font-weight: bold;
    text-align: center;
}

.qr {
    text-align: center;
    margin-top: 20px;
}

.download {
    display: inline-block;
    padding: 10px 15px;
    background: green;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
</style>
</head>

<body>

<!-- WATERMARKS -->
<img src="assets/brain-logo.png" class="wm-logo">

<div class="wm-text">
GLOBAL TRADE HUB • VERIFIED • <?= $reference ?> • <?= htmlspecialchars($order['customer_name']) ?> •
</div>

<div class="receipt-box">

<h2 style="text-align:center;">🧾 Payment Receipt</h2>

<p class="status">PAID ✔</p>

<hr>

<p><b>Product:</b> <?= htmlspecialchars($order['product_name']) ?></p>
<p><b>Customer:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
<p><b>Email:</b> <?= htmlspecialchars($order['email']) ?></p>

<hr>

<p><b>Amount:</b> ₦<?= number_format($order['amount']) ?></p>
<p><b>Reference:</b> <?= htmlspecialchars($order['reference']) ?></p>

<hr>

<p><b>Date:</b> <?= $date ?></p>
<p><b>Time:</b> <?= $time ?></p>

<div class="qr">
    <p><b>Scan to verify</b></p>
    <img src="<?= $qrDataUri ?>" width="180">
</div>

<div style="text-align:center;margin-top:20px;">
    <a class="download" href="download_receipt.php?reference=<?= $order['reference'] ?>">
        Download PDF Receipt
    </a>
</div>

</div>

</body>
</html>
