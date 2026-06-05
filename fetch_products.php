<?php

require "config/db.php";

header("Content-Type: text/html");

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

    echo "
    <div style='background:#fff;padding:15px;border-radius:10px;box-shadow:0 0 10px rgba(0,0,0,.1);'>

        <a href='product.php?id={$p['id']}'>
            <img src='{$p['image_url']}' style='width:100%;height:200px;object-fit:cover;border-radius:10px;'>
        </a>

        <h3>{$p['name']}</h3>

        <p>{$p['description']}</p>

        <b>₦" . number_format((int)$p['price']) . "</b><br><br>

        <a href='order.php?id={$p['id']}' style='
            display:inline-block;
            padding:10px 15px;
            background:#007BFF;
            color:#fff;
            text-decoration:none;
            border-radius:5px;
        '>Order Now</a>

    </div>";
}
