<?php

include "includes/header.php";
require "config/db.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Product not found");
}

// Fetch product safely
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([":id" => $id]);

$product = $stmt->fetch();

if (!$product) {
    die("Product not found");
}

?>

<div style="max-width:800px; margin:auto; padding:20px;">

    <!-- PRODUCT IMAGE -->
    <img src="<?= htmlspecialchars($product['image_url']) ?>"
         style="
            width:100%;
            border-radius:10px;
            max-height:400px;
            object-fit:cover;
         ">

    <!-- PRODUCT NAME -->
    <h2><?= htmlspecialchars($product['name']) ?></h2>

    <!-- PRODUCT DESCRIPTION -->
    <p><?= htmlspecialchars($product['description']) ?></p>

    <!-- PRODUCT PRICE (₦ FIXED) -->
    <h3>₦<?= number_format((int)$product['price']) ?></h3>

    <!-- ORDER BUTTON -->
    <a href="order.php?id=<?= $product['id'] ?>"
       style="
            display:inline-block;
            padding:12px 20px;
            background:green;
            color:white;
            text-decoration:none;
            border-radius:5px;
            margin-top:10px;
       ">
        Order Now
    </a>

</div>

<?php include "includes/footer.php"; ?>
