<?php

require "config/db.php";

header("Content-Type: text/html; charset=UTF-8");

$category = $_GET['category'] ?? null;
$subcategory = $_GET['subcategory'] ?? null;

$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if ($subcategory) {
    $sql .= " AND subcategory = ?";
    $params[] = $subcategory;
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$products = $stmt->fetchAll();

/*
|--------------------------------------------------------------------------
| NO PRODUCTS HANDLING
|--------------------------------------------------------------------------
*/
if (!$products) {
    echo "<p style='text-align:center; width:100%; padding:20px;'>No products found</p>";
    exit;
}

/*
|--------------------------------------------------------------------------
| DISPLAY PRODUCTS
|--------------------------------------------------------------------------
*/

foreach ($products as $p) {

    $name = htmlspecialchars($p['name']);
    $desc = htmlspecialchars($p['description']);
    $price = number_format((int)$p['price']);
    $img = htmlspecialchars($p['image_url']);
    $id = $p['id'];

    echo "
    <div class='product-card' style='
        background:#fff;
        padding:15px;
        border-radius:10px;
        box-shadow:0 0 10px rgba(0,0,0,.1);
        margin-bottom:15px;
    '>

        <a href='product.php?id=$id'>
            <img src='$img' style='width:100%; height:200px; object-fit:cover; border-radius:8px;'>
        </a>

        <h3>$name</h3>

        <p>$desc</p>

        <b>₦$price</b>
        <br><br>
    ";

    /*
    |--------------------------------------------------------------------------
    | SELLER TAG
    |--------------------------------------------------------------------------
    */
    if (!empty($p["seller_id"])) {
        echo "<small style='color:green;'>✔ Seller Product</small><br><br>";
    }

    echo "
        <a href='order.php?id=$id' style='
            display:inline-block;
            padding:10px 15px;
            background:#007BFF;
            color:#fff;
            text-decoration:none;
            border-radius:5px;
        '>Order Now</a>

    </div>";
}
?>
