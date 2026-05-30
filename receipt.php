<?php

require "config/db.php";

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die("Invalid receipt reference");
}

// 🔒 ONLY PAID ORDERS ALLOWED
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

// DATE/TIME
$date = date("Y-m-d", strtotime($order["created_at"] ?? "now"));
$time = date("H:i:s", strtotime($order["created_at"] ?? "now"));

?>

<div style="max-width:650px;margin:auto;padding:20px;font-family:Arial;">

    <h2>🧾 Secure Payment Receipt</h2>

    <p><b>Status:</b> PAID ✔</p>

    <hr>

    <p><b>Product:</b> <?= htmlspecialchars($order['product_name']) ?></p>
    <p><b>Customer:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><b>Email:</b> <?= htmlspecialchars($order['email']) ?></p>

    <hr>

    <p><b>Amount Paid:</b> ₦<?= number_format($order['amount']) ?></p>
    <p><b>Reference:</b> <?= htmlspecialchars($order['reference']) ?></p>

    <hr>

    <p><b>Date:</b> <?= $date ?></p>
    <p><b>Time:</b> <?= $time ?></p>

    <br>

    <a href="download_receipt.php?reference=<?= $order['reference'] ?>"
       style="padding:10px 15px;background:green;color:white;text-decoration:none;">
        Download PDF Receipt
    </a>

</div>
