<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["seller_id"])) {
    die("Access denied");
}

$seller_id = $_SESSION["seller_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        if (
            empty($_POST["name"]) ||
            empty($_POST["price"]) ||
            empty($_POST["description"]) ||
            empty($_POST["category"])
        ) {
            throw new Exception("All fields required");
        }

        if (!isset($_FILES["image"])) {
            throw new Exception("Image required");
        }

        $file = $_FILES["image"]["tmp_name"];
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $path = "uploads/" . $filename;

        move_uploaded_file($file, $path);

        // IMPORTANT: match your existing products schema
        $stmt = $pdo->prepare("
            INSERT INTO products
            (name, description, price, image_url, category, seller_id)
            VALUES
            (:name, :description, :price, :image_url, :category, :seller_id)
        ");

        $stmt->execute([
            ":name" => $_POST["name"],
            ":description" => $_POST["description"],
            ":price" => $_POST["price"],
            ":image_url" => $path,
            ":category" => $_POST["category"],
            ":seller_id" => $seller_id
        ]);

        $message = "Product uploaded successfully";

    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>

<h2>Upload Product</h2>

<?php if ($message): ?>
<p><?= $message ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <input type="text" name="name" placeholder="Product Name" required><br><br>

    <input type="text" name="price" placeholder="Price" required><br><br>

    <textarea name="description" placeholder="Description" required></textarea><br><br>

    <select name="category" required>
        <option value="Clothing">Clothing</option>
        <option value="Food & Beverages">Food & Beverages</option>
        <option value="Electronics">Electronics</option>
    </select><br><br>

    <input type="file" name="image" required><br><br>

    <button type="submit">Upload Product</button>

</form>
