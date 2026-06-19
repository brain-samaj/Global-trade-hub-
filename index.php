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
<?php include "includes/navbar.php"; ?>

<!-- HERO SECTION (WHITE BACKGROUND RESTORED) -->
<section class="hero" style="text-align:center; background:white; padding:80px 20px;">

    <h1>Global Trade Hub</h1>
    <p>Connecting Buyers and Sellers Globally</p>

</section>

<!-- FEATURED PRODUCTS -->
<section style="padding:20px; background:white;">

    <h2 style="text-align:center;">🔥 Featured Products</h2>

    <div class="products">

        <?php
        $stmt = $pdo->query("
            SELECT * FROM products
            ORDER BY id DESC
            LIMIT 6
        ");

        foreach ($stmt as $p):
        ?>

        <div class="card">

            <img
                src="<?=
                    !empty($p['image_url'])
                    ? htmlspecialchars($p['image_url'])
                    : 'assets/default-product.png'
                ?>"
                style="
                    width:100%;
                    height:180px;
                    object-fit:cover;
                    border-radius:10px;
                "
                onerror="this.src='assets/default-product.png'"
            >

            <h3>
                <?= htmlspecialchars($p['name']) ?>
            </h3>

            <p>
                <?= htmlspecialchars($p['description']) ?>
            </p>

            <b>
                ₦<?= number_format((int)$p['price']) ?>
            </b>

            <br><br>

            <a href="add-to-cart.php?id=<?= $p['id'] ?>"
               style="
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
<section style="padding:40px; text-align:center; background:white;">
    <h2>Why Choose Global Trade Hub?</h2>
    <p>Verified sellers • Secure payments • Fast delivery</p>
</section>

<!-- CATEGORIES -->
<section style="padding:20px; text-align:center; background:white;">
    <h2>Browse Categories</h2>

    <a href="products.php?category=Clothing">Clothing</a> |
    <a href="products.php?category=Food">Food</a> |
    <a href="products.php?category=Electronics">Electronics</a>
</section>

<!-- CTA -->
<section style="padding:50px; text-align:center; background:white;">
    <h2>Start Selling Today</h2>

    <a href="become-seller.php" class="btn">
        Become a Seller (click to register)
    </a>
</section>

<?php include "includes/footer.php"; ?>
