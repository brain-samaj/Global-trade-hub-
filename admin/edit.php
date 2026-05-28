<?php

require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid product ID");
}

// FETCH PRODUCT
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([":id" => $id]);

$product = $stmt->fetch();

if (!$product) {
    die("Product not found");
}

// UPDATE PRODUCT
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $description = trim($_POST["description"]);
    $price = trim($_POST["price"]);

    if (!$name || !$description || !$price) {
        die("All fields are required");
    }

    $stmt = $pdo->prepare("
        UPDATE products
        SET
            name = :name,
            description = :description,
            price = :price
        WHERE id = :id
    ");

    $stmt->execute([
        ":name" => $name,
        ":description" => $description,
        ":price" => $price,
        ":id" => $id
    ]);

    header("Location: dashboard.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="
    font-family:Arial,sans-serif;
    background:#f5f5f5;
    padding:20px;
">

<div style="
    max-width:600px;
    margin:auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
">

<h2>Edit Product</h2>

<!-- PRODUCT IMAGE -->
<img src="<?= htmlspecialchars($product['image_url']) ?>"
     style="
        width:100%;
        height:300px;
        object-fit:cover;
        border-radius:10px;
        margin-bottom:20px;
     ">

<form method="POST">

    <!-- NAME -->
    <label>Product Name</label><br>
    <input type="text"
           name="name"
           value="<?= htmlspecialchars($product['name']) ?>"
           style="
                width:100%;
                padding:10px;
                margin-top:5px;
                margin-bottom:15px;
           ">

    <!-- DESCRIPTION -->
    <label>Description</label><br>
    <textarea name="description"
              style="
                    width:100%;
                    padding:10px;
                    height:120px;
                    margin-top:5px;
                    margin-bottom:15px;
              "><?= htmlspecialchars($product['description']) ?></textarea>

    <!-- PRICE -->
    <label>Price</label><br>
    <input type="text"
           name="price"
           value="<?= htmlspecialchars($product['price']) ?>"
           style="
                width:100%;
                padding:10px;
                margin-top:5px;
                margin-bottom:20px;
           ">

    <!-- UPDATE BUTTON -->
    <button type="submit"
            style="
                background:green;
                color:white;
                border:none;
                padding:12px 20px;
                border-radius:5px;
                cursor:pointer;
            ">
        Update Product
    </button>

</form>

</div>

</body>
</html>
