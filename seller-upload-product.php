<?php
session_start();
require "config/db.php";

/*
|--------------------------------------------------------------------------
| AUTH CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "seller") {
    exit("Access denied: Sellers only.");
}

$seller_id = $_SESSION["user_id"];
$message = "";

/*
|--------------------------------------------------------------------------
| HANDLE PRODUCT UPLOAD
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

        $fileTmp = $_FILES["image"]["tmp_name"];
        $fileName = $_FILES["image"]["name"];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowed)) {
            throw new Exception("Only JPG, PNG, WEBP allowed");
        }

        $uploadDir = "uploads/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newFileName = time() . "_" . rand(1000,9999) . "." . $fileExt;
        $filePath = $uploadDir . $newFileName;

        if (!move_uploaded_file($fileTmp, $filePath)) {
            throw new Exception("Failed to save image");
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE PRODUCT TO DB
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
            ":image_url" => $filePath,
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
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<h2>📦 Upload New Product</h2>

<?php if ($message): ?>
    <p style="padding:10px; background:#dff0d8; border-radius:5px;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" style="background:white; padding:20px; border-radius:10px;">

    <input type="text" name="name" placeholder="Product Name" required><br><br>

    <input type="number" name="price" placeholder="Price" required><br><br>

    <textarea name="description" placeholder="Description" required></textarea><br><br>

    <select name="category" required>
        <option value="Clothing">Clothing</option>
        <option value="Food">Food</option>
        <option value="Electronics">Electronics</option>
        <option value="Services">Services</option>
    </select><br><br>

    <input type="file" name="image" required><br><br>

    <button type="submit" style="padding:10px; background:green; color:white;">
        Upload Product
    </button>

</form>

</body>
</html>
