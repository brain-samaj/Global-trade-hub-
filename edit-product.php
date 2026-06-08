<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$role = $_SESSION["role"];

$id = $_GET["id"] ?? null;
if (!$id) die("Invalid product ID");

/*
|------------------------------------------------
| FETCH PRODUCT
|------------------------------------------------
*/

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([":id" => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found");
}

/*
|------------------------------------------------
| PERMISSION CHECK
|------------------------------------------------
| Admin = full access
| Seller = only own product
*/

if ($role !== "admin" && $product["seller_id"] != $user_id) {
    exit("Access denied");
}

/*
|------------------------------------------------
| UPDATE PRODUCT
|------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $price = trim($_POST["price"]);
    $description = trim($_POST["description"]);

    $image_url = $product["image_url"];

    /*
    |--------------------------------------------
    | IMAGE UPDATE
    |--------------------------------------------
    */

    if (!empty($_FILES["image"]["name"])) {

        $allowed = ["jpg", "jpeg", "png", "webp"];
        $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            die("Invalid image type");
        }

        if (!is_dir("uploads")) {
            mkdir("uploads", 0777, true);
        }

        $newName = time() . rand(1000,9999) . "." . $ext;
        $path = "uploads/" . $newName;

        move_uploaded_file($_FILES["image"]["tmp_name"], $path);

        $image_url = $path;
    }

    /*
    |--------------------------------------------
    | UPDATE QUERY
    |--------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE products
        SET name = :name,
            price = :price,
            description = :description,
            image_url = :image
        WHERE id = :id
    ");

    $stmt->execute([
        ":name" => $name,
        ":price" => $price,
        ":description" => $description,
        ":image" => $image_url,
        ":id" => $id
    ]);

    // redirect based on role
    if ($role === "admin") {
        header("Location: admin/dashboard.php?updated=1");
    } else {
        header("Location: seller-dashboard.php?updated=1");
    }
    exit();
}
?>

<h2>Edit Product</h2>

<form method="POST" enctype="multipart/form-data">

    <input type="text" name="name"
        value="<?= htmlspecialchars($product['name']) ?>"
        required>

    <input type="number" name="price"
        value="<?= htmlspecialchars($product['price']) ?>"
        required>

    <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

    <br><br>

    <img src="<?= htmlspecialchars($product['image_url']) ?>" width="150">

    <br><br>

    <input type="file" name="image">

    <br><br>

    <button type="submit">Update Product</button>

</form>
