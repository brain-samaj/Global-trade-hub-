<?php

include "includes/header.php";
require "config/db.php";

// Fetch products from PostgreSQL safely
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

?>

<h2 style="text-align:center;">Our Products</h2>

<section class="products" style="
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
    gap:20px;
    padding:20px;
">

<?php if (!$products): ?>
    <p style="text-align:center;">No products available yet.</p>
<?php endif; ?>

<?php foreach ($products as $p): ?>

<div class="card" style="
    background:#fff;
    padding:15px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
">

    <!-- PRODUCT IMAGE -->
    <img src="<?= htmlspecialchars($p['image_url']) ?>"
         style="width:100%; border-radius:10px; height:200px; object-fit:cover;">

    <!-- PRODUCT NAME -->
    <h3><?= htmlspecialchars($p['name']) ?></h3>

    <!-- DESCRIPTION -->
    <p><?= htmlspecialchars($p['description']) ?></p>

    <!-- PRICE -->
    <p><b>$<?= htmlspecialchars($p['price']) ?></b></p>

    <!-- ORDER BUTTON -->
    <a href="order.php?id=<?= $p['id'] ?>" style="
        display:inline-block;
        padding:10px 15px;
        background:#007BFF;
        color:#fff;
        text-decoration:none;
        border-radius:5px;
        margin-top:10px;
    ">
        Order Now
    </a>

</div>

<?php endforeach; ?>

</section>

<?php include "includes/footer.php"; ?>
