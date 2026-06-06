<?php include "includes/header.php"; ?>

<!-- HERO SECTION -->
<section class="hero">
    <h1>Global Trade Hub</h1>
    <p>Connecting African Products to the World</p>

    <a href="products.php" class="btn">Explore Products</a>

    <a href="become-seller.php" class="btn" style="background:#28a745;">
        Become a Seller
    </a>
</section>

<!-- FEATURED PRODUCTS -->
<section style="padding:20px;">

    <h2 style="text-align:center;">🔥 Featured Products</h2>

    <div class="products">

        <?php
        require "config/db.php";

        $stmt = $pdo->query("
            SELECT * FROM products
            ORDER BY id DESC
            LIMIT 6
        ");

        foreach ($stmt as $p):
        ?>

        <div class="card">

            <img src="<?= htmlspecialchars($p['image_url']) ?>">

            <h3><?= htmlspecialchars($p['name']) ?></h3>

            <p><?= htmlspecialchars($p['description']) ?></p>

            <b>₦<?= number_format((int)$p['price']) ?></b>

            <br><br>

            <a href="order.php?id=<?= $p['id'] ?>" class="btn">
                Order Now
            </a>

        </div>

        <?php endforeach; ?>

    </div>

</section>

<!-- WHY US -->
<section style="padding:40px; text-align:center;">

    <h2>Why Choose Global Trade Hub?</h2>

    <p>Verified sellers • Secure payments • Fast delivery network</p>

</section>

<!-- CATEGORIES -->
<section style="padding:20px; text-align:center;">

    <h2>Browse Categories</h2>

    <a href="products.php?category=Clothing" class="btn">Clothing</a>
    <a href="products.php?category=Food" class="btn">Food</a>
    <a href="products.php?category=Electronics" class="btn">Electronics</a>

</section>

<!-- FINAL CTA -->
<section style="padding:50px; text-align:center; background:#0a1f44; color:white;">

    <h2>Start Selling Today</h2>

    <a href="become-seller.php" class="btn" style="background:#28a745;">
        Become a Seller (click to register)
    </a>

</section>

<?php include "includes/footer.php"; ?>
