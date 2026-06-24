<?php

require "config/db.php";
require "vendor/autoload.php";

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;

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

// 🔐 VERIFY LINK
$verifyUrl = "https://global-trade-hub-3nbz.onrender.com/verify_receipt.php?reference=" . $order['reference'];

// ✅ QR (SVG - NO GD REQUIRED)
$result = Builder::create()
    ->writer(new SvgWriter())
    ->data($verifyUrl)
    ->size(220)
    ->margin(10)
    ->build();

$qrSvg = $result->getString();

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

.receipt {
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
}

.watermark {
    position: fixed;
    top: 50%;
    left: 50%;
    font-size: 40px;
    opacity: 0.05;
    transform: translate(-50%, -50%);
    z-index: 0;
    pointer-events: none;
}
</style>
</head>

<body>

<div class="watermark">
GLOBAL TRADE HUB • VERIFIED
</div>

<div class="receipt">

<h2>🧾 Payment Receipt</h2>

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

<hr>

<h4>Scan to Verify</h4>

<!-- ✅ QR CODE (SVG OUTPUT) -->
<div>
    <?= $qrSvg ?>
</div>

<br>

<a href="download_receipt.php?reference=<?= $order['reference'] ?>">
    Download PDF Receipt
</a>

<br><br>

<a href="buyer-dashboard.php"
   style="
   background:#0d6efd;
   color:white;
   padding:10px 15px;
   text-decoration:none;
   border-radius:5px;
   ">
   ← Back to Dashboard
</a>

<a href="orders.php"
   style="
   background:green;
   color:white;
   padding:10px 15px;
   text-decoration:none;
   border-radius:5px;
   margin-left:10px;
   ">
   📦 My Orders
</a>

</div>

</body>
</html>
