<?php

require "../config/db.php";
require "../config/cloudinary.php";
require "../includes/auth.php";

checkAdmin();

$message = "";

if (isset($_GET["success"])) {
    $message = "Product uploaded successfully!";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        if (
            empty($_POST["name"]) ||
            empty($_POST["price"]) ||
            empty($_POST["desc"]) ||
            empty($_POST["category"]) ||
            empty($_POST["subcategory"]) ||
            empty($_POST["sub_subcategory"])
        ) {
            throw new Exception("All fields are required");
        }

        if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
            throw new Exception("Image upload failed");
        }

        $fileTmp = $_FILES["image"]["tmp_name"];

        $uploadResult = $cloudinary->uploadApi()->upload($fileTmp, [
            "folder" => "global_trade_products"
        ]);

        if (!isset($uploadResult["secure_url"])) {
            throw new Exception("Cloudinary upload failed");
        }

        $imageUrl = $uploadResult["secure_url"];

        $price = (int) preg_replace('/[^0-9]/', '', $_POST["price"]);

        $stmt = $pdo->prepare("
            INSERT INTO products
            (name, description, price, image_url, category, subcategory, sub_subcategory)
            VALUES
            (:name, :description, :price, :image_url, :category, :subcategory, :sub_subcategory)
        ");

        $stmt->execute([
            ":name" => trim($_POST["name"]),
            ":description" => trim($_POST["desc"]),
            ":price" => $price,
            ":image_url" => $imageUrl,
            ":category" => $_POST["category"],
            ":subcategory" => $_POST["subcategory"],
            ":sub_subcategory" => $_POST["sub_subcategory"]
        ]);

        header("Location: upload.php?success=1");
        exit;

    } catch (Exception $e) {
        $message = "ERROR: " . $e->getMessage();
    }
}
?>

<h2>Upload Product</h2>

<?php if ($message): ?>
<p style="padding:10px;background:#eee;">
    <?= $message ?>
</p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

<input name="name" placeholder="Product Name" required><br><br>
<input name="price" placeholder="Price" required><br><br>
<textarea name="desc" placeholder="Description" required></textarea><br><br>

<select name="category" required>
    <option value="">Category</option>
    <option>Clothing</option>
    <option>Food & Beverages</option>
    <option>Electronics</option>
</select><br><br>

<select name="subcategory" required>
    <option value="">Subcategory</option>
    <option>Male</option>
    <option>Female</option>
    <option>Beverages</option>
    <option>Grains</option>
    <option>Phones</option>
    <option>Laptops</option>
</select><br><br>

<select name="sub_subcategory" required>
    <option value="">Type</option>
    <option>Men's Wear</option>
    <option>Boys' Wear</option>
    <option>Women's Wear</option>
    <option>Girls' Wear</option>
    <option>Men's Shoes</option>
    <option>Others</option>
</select><br><br>

<input type="file" name="image" required><br><br>

<button type="submit">Upload</button>

</form>
