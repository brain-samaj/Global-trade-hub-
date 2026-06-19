<?php include "includes/header.php"; ?><?php require "config/db.php"; ?><h2 style="text-align:center;margin-top:20px;">
<?php include "includes/navbar.php"; ?>

    Our Products
</h2><!-- SEARCH BOX --><div style="
    text-align:center;
    padding:15px;
"><input
    type="text"
    id="searchBox"
    placeholder="Search products..."
    onkeyup="searchProducts()"
    style="
        width:90%;
        max-width:500px;
        padding:12px;
        border:1px solid #ccc;
        border-radius:8px;
    "
>

</div><!-- CATEGORY BUTTONS --><div style="
    text-align:center;
    margin-bottom:20px;
"><button onclick="loadProducts()" style="
    padding:10px 15px;
    margin:5px;
    border:none;
    border-radius:6px;
    cursor:pointer;
">
    All Products
</button>

<button onclick="toggleCategory('clothing')" style="
    padding:10px 15px;
    margin:5px;
    border:none;
    border-radius:6px;
    cursor:pointer;
">
    👕 Clothing
</button>

<button onclick="toggleCategory('food')" style="
    padding:10px 15px;
    margin:5px;
    border:none;
    border-radius:6px;
    cursor:pointer;
">
    🍔 Food & Beverages
</button>

<button onclick="toggleCategory('electronics')" style="
    padding:10px 15px;
    margin:5px;
    border:none;
    border-radius:6px;
    cursor:pointer;
">
    💻 Electronics
</button>

</div><!-- CLOTHING --><div id="clothing"
     style="
        display:none;
        text-align:center;
        margin-bottom:15px;
"><button onclick="loadProducts('Clothing','Male')">
    Male
</button>

<button onclick="loadProducts('Clothing','Female')">
    Female
</button>

</div><!-- FOOD --><div id="food"
     style="
        display:none;
        text-align:center;
        margin-bottom:15px;
"><button onclick="loadProducts('Food & Beverages')">
    Food & Beverages
</button>

</div><!-- ELECTRONICS --><div id="electronics"
     style="
        display:none;
        text-align:center;
        margin-bottom:15px;
"><button onclick="loadProducts('Electronics')">
    Electronics
</button>

</div><!-- PRODUCTS --><div id="productArea"
     style="
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
        gap:20px;
        padding:20px;
"><?php
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");

foreach ($stmt as $p):
?><div class="product-card"
     style="
        background:#fff;
        padding:15px;
        border-radius:10px;
        box-shadow:0 0 10px rgba(0,0,0,.1);
"><a href="product.php?id=<?= $p['id'] ?>">
    <img
        src="<?= htmlspecialchars($p['image_url']) ?>"
        style="
            width:100%;
            border-radius:10px;
        "
    >
</a>

<h3><?= htmlspecialchars($p['name']) ?></h3>

<p><?= htmlspecialchars($p['description']) ?></p>

<b>₦<?= number_format((int)$p['price']) ?></b>

<br><br>

<a href="order.php?id=<?= $p['id'] ?>"
   style="
        padding:10px 15px;
        background:#007BFF;
        color:#fff;
        text-decoration:none;
        border-radius:5px;
        display:inline-block;
   ">
    Order Now
</a>

</div><?php endforeach; ?></div><script>

function toggleCategory(id){

    document.getElementById('clothing').style.display = 'none';
    document.getElementById('food').style.display = 'none';
    document.getElementById('electronics').style.display = 'none';

    document.getElementById(id).style.display = 'block';
}

function searchProducts(){

    let input =
        document.getElementById('searchBox')
        .value
        .toLowerCase();

    let cards =
        document.querySelectorAll('.product-card');

    cards.forEach(card => {

        let text =
            card.innerText.toLowerCase();

        card.style.display =
            text.includes(input)
            ? 'block'
            : 'none';
    });
}

function loadProducts(category = null, subcategory = null){

    let url = "fetch_products.php?";

    if(category){
        url += "category=" +
        encodeURIComponent(category);
    }

    if(subcategory){
        url += "&subcategory=" +
        encodeURIComponent(subcategory);
    }

    fetch(url)
    .then(res => res.text())
    .then(data => {

        document.getElementById(
            "productArea"
        ).innerHTML = data;

    });
}

</script><?php include "includes/footer.php"; ?>
