<?php
session_start();

if (!isset($_SESSION['loaded'])) {
    $_SESSION['loaded'] = true;
    header("Location: loading.php");
    exit;
}

require "config/db.php";
?>

<?php include "includes/header.php"; ?>

<!-- HERO SECTION -->
<section class="hero" style="text-align:center; padding:40px;">

    <h1>Global Trade Hub</h1>

    <p>Connecting African Products to the World</p>

    <!-- AUTH BUTTONS -->
    <div style="margin:20px 0;">
        <a href="login.php" class="btn">Sign In</a>
        <a href="buyer-register.php" class="btn">Sign Up</a>
        <a href="become-seller.php" class="btn">Become a Seller</a>
    </div>

    <a href="products.php" class="btn">Explore Products</a>

</section>

<!-- FEATURED PRODUCTS -->
<section style="padding:20px;">

    <h2 style="text-align:center;">🔥 Featured Products</h2>

    <div class="products" style="
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
        gap:15px;
        margin-top:20px;
    ">

        <?php
        $stmt = $pdo->query("
            SELECT * FROM products
            ORDER BY id DESC
            LIMIT 6
        ");

        foreach ($stmt as $p):
        ?>

        <div class="card" style="
            background:white;
            padding:10px;
            border-radius:10px;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
        ">

            <img src="<?= htmlspecialchars($p['image_url']) ?>" style="
                width:100%;
                height:150px;
                object-fit:cover;
                border-radius:8px;
            ">

            <h3><?= htmlspecialchars($p['name']) ?></h3>

            <p><?= htmlspecialchars($p['description']) ?></p>

            <b>₦<?= number_format((int)$p['price']) ?></b>

            <br><br>

            <!-- STEP 4 CART FLOW -->
            <a href="add-to-cart.php?id=<?= $p['id'] ?>" style="
                display:inline-block;
                padding:8px;
                background:#0d47a1;
                color:white;
                text-decoration:none;
                border-radius:5px;
            ">
                Add to Cart
            </a>

        </div>

        <?php endforeach; ?>

    </div>

</section>

<!-- WHY US -->
<section style="padding:40px; text-align:center;">

    <h2>Why Choose Global Trade Hub?</h2>

    <p>Verified sellers • Secure payments • Fast delivery</p>

</section>

<!-- CATEGORIES -->
<section style="padding:20px; text-align:center;">

    <h2>Browse Categories</h2>

    <a href="products.php?category=Clothing">Clothing</a> |
    <a href="products.php?category=Food">Food</a> |
    <a href="products.php?category=Electronics">Electronics</a>

</section>

<!-- FINAL CTA -->
<section style="padding:50px; text-align:center;">

    <h2>Start Selling Today</h2>

    <a href="become-seller.php" class="btn">
        Become a Seller (click to register)
    </a>

</section>

<?php include "includes/footer.php"; ?>
