<?php
session_start();
require "config/db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "Cart is empty";
    exit;
}

/* Get products */
$placeholders = implode(',', array_fill(0, count($cart), '?'));

$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($cart);

$products = $stmt->fetchAll();

$total = 0;
$seller_id = null;

/* Create order */
foreach ($products as $p) {
    $total += (int)$p['price'];
    $seller_id = $p['seller_id']; // assumes single seller per order
}

/* Insert order */
$stmt = $pdo->prepare("
    INSERT INTO orders (
        product_id,
        customer_name,
        email,
        amount,
        status,
        seller_id
    ) VALUES (?, ?, ?, ?, 'pending', ?)
");

$user_id = $_SESSION['user_id'];

$stmt->execute([
    $products[0]['id'],
    "Buyer",
    "buyer@example.com",
    $total,
    $seller_id
]);

/* Clear cart */
unset($_SESSION['cart']);

echo "<h2>Order placed successfully 🎉</h2>";
echo "<a href='index.php'>Back to shop</a>";
?>
