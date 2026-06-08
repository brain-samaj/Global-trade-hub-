<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "seller") {
    header("Location: login.php");
    exit();
}

$seller_id = $_SESSION["user_id"];

$stmt = $pdo->prepare("
    SELECT * FROM products
    WHERE seller_id = :id
    ORDER BY id DESC
");

$stmt->execute([":id" => $seller_id]);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Products</title>
</head>

<body style="font-family:Arial; padding:20px; background:#f5f5f5;">

<h2>📦 My Products</h2>

<a href="seller-upload-product.php" style="padding:10px; background:green; color:white; text-decoration:none;">
    + Add New Product
</a>

<br><br>

<?php foreach ($products as $p): ?>

<div style="background:white; padding:15px; margin-bottom:15px; border-radius:10px;">

    <img src="<?= htmlspecialchars($p['image_url']) ?>"
         style="width:150px; height:150px; object-fit:cover;">

    <h3><?= htmlspecialchars($p['name']) ?></h3>
    <p>₦<?= number_format($p['price']) ?></p>

    <a href="edit-product.php?id=<?= $p['id'] ?>" style="padding:8px; background:orange; color:white; text-decoration:none;">
        Edit
    </a>

    <a href="delete-product.php?id=<?= $p['id'] ?>"
       onclick="return confirm('Delete this product?')"
       style="padding:8px; background:red; color:white; text-decoration:none;">
        Delete
    </a>

</div>

<?php endforeach; ?>

</body>
</html>
