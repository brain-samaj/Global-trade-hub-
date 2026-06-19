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

    <div class="products" style="
        display:grid;
        grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
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

<?php
$image = $p['image'];
?>

<img src="<?= htmlspecialchars($image) ?>"
     onerror="this.src='assets/images/default.png'"
     style="width:100%; height:150px; object-fit:cover; border-radius:8px;">
            <h3><?= htmlspecialchars($p['name'] ?? 'No name') ?></h3>

            <p><?= htmlspecialchars($p['description'] ?? 'No description') ?></p>

            <b>₦<?= number_format((int)($p['price'] ?? 0)) ?></b>

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
