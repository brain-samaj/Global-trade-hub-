<?php

require "../config/db.php";
require "../config/cloudinary.php";
require "../includes/auth.php";

checkAdmin();

$message = "";

// Success message
if (isset($_GET["success"])) {
    $message = "Product uploaded successfully!";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        // 1. VALIDATE INPUTS
        if (
            empty($_POST["name"]) ||
            empty($_POST["price"]) ||
            empty($_POST["desc"])
        ) {
            throw new Exception("All fields are required");
        }

        // 2. VALIDATE IMAGE
        if (
            !isset($_FILES["image"]) ||
            $_FILES["image"]["error"] !== UPLOAD_ERR_OK
        ) {
            throw new Exception("Image upload failed or no file selected");
        }

        $fileTmp = $_FILES["image"]["tmp_name"];

        // 3. UPLOAD TO CLOUDINARY
        $uploadResult = $cloudinary->uploadApi()->upload($fileTmp, [
            "folder" => "global_trade_products"
        ]);

        if (!isset($uploadResult["secure_url"])) {
            throw new Exception("Cloudinary upload failed");
        }

        $imageUrl = $uploadResult["secure_url"];

        // 4. INSERT INTO DATABASE
        $stmt = $pdo->prepare("
            INSERT INTO products (name, description, price, image_url)
            VALUES (:name, :description, :price, :image_url)
        ");

        $stmt->execute([
            ":name" => trim($_POST["name"]),
            ":description" => trim($_POST["desc"]),
            ":price" => trim($_POST["price"]),
            ":image_url" => $imageUrl
        ]);

        // 5. REDIRECT (PREVENT DOUBLE SUBMIT)
        header("Location: upload.php?success=1");
        exit;

    } catch (Exception $e) {
        $message = "ERROR: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Product</title>
</head>
<body>

<h2>Upload Product</h2>

<?php if ($message): ?>
    <p style="padding:10px;background:#f2f2f2;color:#000;">
        <?php echo $message; ?>
    </p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Product Name" required><br><br>

    <input type="text" name="price" placeholder="Price" required><br><br>

    <textarea name="desc" placeholder="Description" required></textarea><br><br>

    <input type="file" name="image" required><br><br>

    <button type="submit">Upload Product</button>

</form>

</body>
</html>
