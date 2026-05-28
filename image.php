<?php

require "config/db.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Image not found");
}

$stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = :id");
$stmt->execute([":id" => $id]);
$product = $stmt->fetch();

if (!$product) {
    die("Image not found");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Image View</title>
</head>
<body style="margin:0;background:#000;">

<!-- BACK BUTTON -->
<a href="products.php" style="
    position:fixed;
    top:15px;
    left:15px;
    background:#fff;
    padding:10px 15px;
    border-radius:5px;
    text-decoration:none;
    font-weight:bold;
    z-index:999;
">
    ← Back
</a>

<!-- IMAGE -->
<div style="display:flex;justify-content:center;align-items:center;height:100vh;">
    <img src="<?= htmlspecialchars($product['image_url']) ?>"
         style="max-width:95%; max-height:95%; object-fit:contain;">
</div>

</body>
</html>
