<?php
session_start();
require "config/db.php";

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<h2>Your cart is empty</h2>";
    echo "<a href='index.php'>Go back</a>";
    exit;
}

/* Fetch products in cart */
$placeholders = implode(',', array_fill(0, count($cart), '?'));

$stmt = $pdo->prepare("
    SELECT * FROM products WHERE id IN ($placeholders)
");
$stmt->execute($cart);

$products = $stmt->fetchAll();

$total = 0;
?>

<h2>Your Cart 🛒</h2>

<?php foreach ($products as $p): ?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px;">
        <h3><?= htmlspecialchars($p['name']) ?></h3>
        <p>₦<?= number_format($p['price']) ?></p>
    </div>

    <?php $total += (int)$p['price']; ?>
<?php endforeach; ?>

<h3>Total: ₦<?= number_format($total) ?></h3>

<br>

<a href="checkout.php">Proceed to Checkout</a>
