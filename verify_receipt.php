<?php

require "config/db.php";

$reference = $_GET['reference'] ?? null;

if (!$reference) {
    die("Invalid verification request");
}

// 🔒 FETCH ONLY PAID TRANSACTIONS
$stmt = $pdo->prepare("
    SELECT o.*, p.name AS product_name
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE o.reference = :ref
    AND o.status = 'paid'
");

$stmt->execute([":ref" => $reference]);
$order = $stmt->fetch();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Receipt Verification</title>

    <style>
        body {
            font-family: Arial;
            background: #f5f5f5;
        }

        .box {
            max-width: 600px;
            margin: 60px auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.1);
            text-align: center;
        }

        .valid {
            color: green;
            font-size: 22px;
            font-weight: bold;
        }

        .invalid {
            color: red;
            font-size: 22px;
            font-weight: bold;
        }

        .info {
            text-align: left;
            margin-top: 20px;
        }

        .info p {
            padding: 5px 0;
        }

        .badge {
            display: inline-block;
            padding: 8px 12px;
            background: green;
            color: white;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>

<body>

<div class="box">

<?php if ($order): ?>

    <div class="valid">✔ VERIFIED TRANSACTION</div>

    <div class="badge">PAID ✔</div>

    <div class="info">
        <p><b>Product:</b> <?= htmlspecialchars($order['product_name']) ?></p>
        <p><b>Customer:</b> <?= htmlspecialchars($order['customer_name']) ?></p>
        <p><b>Email:</b> <?= htmlspecialchars($order['email']) ?></p>
        <p><b>Amount:</b> ₦<?= number_format($order['amount']) ?></p>
        <p><b>Reference:</b> <?= htmlspecialchars($order['reference']) ?></p>
    </div>

<?php else: ?>

    <div class="invalid">❌ INVALID OR UNVERIFIED TRANSACTION</div>

    <p>This receipt cannot be verified. It may be fake or unpaid.</p>

<?php endif; ?>

</div>

</body>
</html>
