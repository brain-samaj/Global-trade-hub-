<?php
session_start();
require "../config/db.php";

$seller_id = $_SESSION["seller_id"] ?? null;

if (!$seller_id) {
    die("Access denied");
}

$stmt = $pdo->prepare("
    SELECT * FROM sellers WHERE id = :id
");
$stmt->execute([":id" => $seller_id]);
$seller = $stmt->fetch();
?>

<h2>Your Wallet</h2>

<p><strong>Balance:</strong> ₦<?= number_format($seller["wallet_balance"]) ?></p>

<p><strong>Total Earned:</strong> ₦<?= number_format($seller["total_earned"]) ?></p>

<p><strong>Total Withdrawn:</strong> ₦<?= number_format($seller["total_withdrawn"]) ?></p>

<a href="withdraw.php">Request Withdrawal</a>
