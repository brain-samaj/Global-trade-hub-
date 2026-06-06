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

if (!$products) {
    echo "<p style='text-align:center;width:100%'>No products found</p>";
    exit;
}

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
    '>

        <a href='product.php?id=$id'>
            <img src='$img' style='width:100%;border-radius:10px;'>
        </a>

        <h3>$name</h3>

        <p>$desc</p>

        <b>₦$price</b>
        <br><br>
    ";

    // 🟢 SELLER TAG (NEW FEATURE)
    if (!empty($p["seller_id"])) {
        echo "<small style='color:green'>Seller Product</small><br><br>";
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
