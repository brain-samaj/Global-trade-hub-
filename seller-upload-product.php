<?php
session_start();
require "config/db.php";

/*
|--------------------------------------------------------------------------
| AUTH CHECK (NEW SYSTEM)
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["role"] !== "seller") {
    exit("Access denied: Sellers only.");
}

$seller_id = $_SESSION["user_id"];
$message = "";

/*
|--------------------------------------------------------------------------
| HANDLE UPLOAD
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $name = trim($_POST["name"]);
        $price = trim($_POST["price"]);
        $description = trim($_POST["description"]);
        $category = trim($_POST["category"]);

        if ($name === "" || $price === "" || $description === "" || $category === "") {
            throw new Exception("All fields are required");
        }

        if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== 0) {
            throw new Exception("Image upload failed");
        }

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPLOAD (LOCAL STORAGE)
        |--------------------------------------------------------------------------
        */

        $allowed = ["jpg", "jpeg", "png", "webp"];
        $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            throw new Exception("Invalid image type (jpg, png, webp only)");
        }

        $filename = time() . "_" . rand(1000,9999) . "." . $ext;
        $uploadDir = "uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filepath = $uploadDir . $filename;

        move_uploaded_file($_FILES["image"]["tmp_name"], $filepath);

        /*
        |--------------------------------------------------------------------------
        | INSERT PRODUCT
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO products
            (name, description, price, image_url, category, seller_id)
            VALUES
            (:name, :description, :price, :image_url, :category, :seller_id)
        ");

        $stmt->execute([
            ":name" => $name,
            ":description" => $description,
            ":price" => $price,
            ":image_url" => $filepath,
            ":category" => $category,
            ":seller_id" => $seller_id
        ]);

        $message = "Product uploaded successfully!";

    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<h2>📦 Upload New Product</h2>

<?php if ($message): ?>
    <p style="padding:10px; background:#e0ffe0; border:1px solid green;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<!-- FORM CARD -->
<div style="background:white; padding:20px; border-radius:10px; max-width:500px;">

<form method="POST" enctype="multipart/form-data">

    <label>Product Name</label><br>
    <input type="text" name="name" style="width:100%; padding:10px;" required>
    <br><br>

    <label>Price</label><br>
    <input type="number" name="price" style="width:100%; padding:10px;" required>
    <br><br>

    <label>Description</label><br>
    <textarea name="description" style="width:100%; padding:10px;" required></textarea>
    <br><br>

    <label>Category</label><br>
    <select name="category" style="width:100%; padding:10px;" required>
        <option value="Clothing">Clothing</option>
        <option value="Food">Food</option>
        <option value="Electronics">Electronics</option>
        <option value="Services">Services</option>
    </select>
    <br><br>

    <label>Product Image</label><br>
    <input type="file" name="image" required>
    <br><br>

    <button type="submit"
        style="width:100%; padding:12px; background:green; color:white; border:none; border-radius:5px;">
        Upload Product
    </button>

</form>

</div>

</body>
</html>
