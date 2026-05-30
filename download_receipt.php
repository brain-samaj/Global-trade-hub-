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

// 🔒 MUST BE PAID
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

$html = "
<h2>Payment Receipt</h2>
<hr>

<p><b>Status:</b> PAID ✔</p>
<p><b>Product:</b> {$order['product_name']}</p>
<p><b>Customer:</b> {$order['customer_name']}</p>
<p><b>Email:</b> {$order['email']}</p>

<hr>

<p><b>Amount:</b> ₦" . number_format($order['amount']) . "</p>
<p><b>Reference:</b> {$order['reference']}</p>

<hr>

<p><b>Date:</b> {$date}</p>
<p><b>Time:</b> {$time}</p>

<hr>

<p>Global Trade Hub</p>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

$dompdf->stream("receipt_" . $reference . ".pdf", ["Attachment" => true]);
