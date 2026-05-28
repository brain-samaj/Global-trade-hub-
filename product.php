<?php

include "includes/header.php";
require "config/db.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Product not found");
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([":id" => $id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found");
}

?>

<div style="max-width:800px;margin:auto;padding:20px;">

    <img src="<?= htmlspecialchars($product['image_url']) ?>"
         style="width:100%; border-radius:10px; height:300px; object-fit:cover;">

    <h2><?= htmlspecialchars($product['name']) ?></h2>

    <p><?= htmlspecialchars($product['description']) ?></p>

    <?php
        $price = str_replace('$', '', $product['price']);
    ?>

    <h3>$<?= htmlspecialchars($price) ?></h3>

    <a href="order.php?id=<?= $product['id'] ?>" style="
        display:inline-block;
        padding:12px 20px;
        background:green;
        color:#fff;
        text-decoration:none;
        border-radius:5px;
    ">
        Order Now
    </a>

</div>

<?php include "includes/footer.php"; ?>
