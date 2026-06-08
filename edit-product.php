<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "seller") {
    header("Location: login.php");
    exit();
}

$id = $_GET["id"] ?? null;
if (!$id) die("Invalid product");

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id AND seller_id = :sid");
$stmt->execute([
    ":id" => $id,
    ":sid" => $_SESSION["user_id"]
]);

$product = $stmt->fetch();

if (!$product) die("Product not found");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = $_POST["name"];
    $price = $_POST["price"];
    $description = $_POST["description"];

    $image_url = $product["image_url"];

    // if new image uploaded
    if (!empty($_FILES["image"]["name"])) {

        $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
        $newName = time() . rand(1000,9999) . "." . $ext;

        $path = "uploads/" . $newName;

        move_uploaded_file($_FILES["image"]["tmp_name"], $path);

        $image_url = $path;
    }

    $stmt = $pdo->prepare("
        UPDATE products
        SET name=:name, price=:price, description=:description, image_url=:image
        WHERE id=:id AND seller_id=:sid
    ");

    $stmt->execute([
        ":name" => $name,
        ":price" => $price,
        ":description" => $description,
        ":image" => $image_url,
        ":id" => $id,
        ":sid" => $_SESSION["user_id"]
    ]);

    header("Location: seller-products.php");
    exit();
}
?>

<h2>Edit Product</h2>

<form method="POST" enctype="multipart/form-data">

    <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>"><br><br>

    <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>"><br><br>

    <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

    <img src="<?= $product['image_url'] ?>" width="120"><br>

    <input type="file" name="image"><br><br>

    <button type="submit">Update Product</button>

</form>
