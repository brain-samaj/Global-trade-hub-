<?php include "includes/header.php"; ?>
<?php require "config/db.php"; ?>

<h2 style="text-align:center;">Our Products</h2>

<div style="display:flex;">

<!-- SIDEBAR -->
<aside style="width:220px;padding:15px;background:#f5f5f5;">

    <h3>Categories</h3>

    <button onclick="loadProducts()">All Products</button><br><br>

    <b>Clothing</b><br>
    <button onclick="loadProducts('Clothing')">All Clothing</button><br>
    <button onclick="loadProducts('Clothing','Male')">Male</button><br>
    <button onclick="loadProducts('Clothing','Female')">Female</button><br><br>

    <b>Food</b><br>
    <button onclick="loadProducts('Food & Beverages')">Food & Beverages</button><br><br>

    <b>Electronics</b><br>
    <button onclick="loadProducts('Electronics')">Electronics</button>

</aside>

<!-- PRODUCTS AREA -->
<div id="productArea" style="
    flex:1;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    padding:20px;
">

<?php
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
foreach ($stmt as $p):
?>

<div style="background:#fff;padding:15px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,.1);">

    <a href="product.php?id=<?= $p['id'] ?>">
        <img src="<?= $p['image_url'] ?>" style="width:100%;border-radius:10px;">
    </a>

    <h3><?= htmlspecialchars($p['name']) ?></h3>

    <p><?= htmlspecialchars($p['description']) ?></p>

    <b>₦<?= number_format((int)$p['price']) ?></b>

    <br><br>

    <a href="order.php?id=<?= $p['id'] ?>" style="
        padding:10px 15px;
        background:#007BFF;
        color:#fff;
        text-decoration:none;
        border-radius:5px;
        display:inline-block;
    ">Order Now</a>

</div>

<?php endforeach; ?>

</div>

</div>

<!-- AJAX SCRIPT -->
<script>
function loadProducts(category = null, subcategory = null) {

    let url = "fetch_products.php?";

    if (category) url += "category=" + encodeURIComponent(category);
    if (subcategory) url += "&subcategory=" + encodeURIComponent(subcategory);

    fetch(url)
    .then(res => res.text())
    .then(data => {
        document.getElementById("productArea").innerHTML = data;
    });
}
</script>

<?php include "includes/footer.php"; ?>
