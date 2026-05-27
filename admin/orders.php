<?php
require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

$stmt = $pdo->query("
    SELECT orders.*, products.name AS product_name
    FROM orders
    JOIN products ON orders.product_id = products.id
    ORDER BY orders.id DESC
");

$orders = $stmt->fetchAll();
?>

<h2>Customer Orders</h2>

<?php foreach ($orders as $o): ?>

<div style="border:1px solid #ccc;padding:10px;margin-bottom:10px;">
    <p><b>Product:</b> <?= htmlspecialchars($o['product_name']) ?></p>
    <p><b>Name:</b> <?= htmlspecialchars($o['customer_name']) ?></p>
    <p><b>Phone:</b> <?= htmlspecialchars($o['phone']) ?></p>
    <p><b>Status:</b> <?= htmlspecialchars($o['status']) ?></p>
</div>

<?php endforeach; ?>
