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

        if (
            !isset($_FILES["image"]) ||
            $_FILES["image"]["error"] !== UPLOAD_ERR_OK
        ) {
            throw new Exception("Image upload failed");
        }

        $uploadResult = $cloudinary->uploadApi()->upload(
            $_FILES["image"]["tmp_name"],
            [
                "folder" => "global_trade_products"
            ]
        );

        if (!isset($uploadResult["secure_url"])) {
            throw new Exception("Cloudinary upload failed");
        }

        $imageUrl = $uploadResult["secure_url"];

        $price = (int) preg_replace(
            '/[^0-9]/',
            '',
            $_POST["price"]
        );

        $stmt = $pdo->prepare("
            INSERT INTO products
            (
                name,
                description,
                price,
                image_url,
                category,
                subcategory,
                sub_subcategory
            )
            VALUES
            (
                :name,
                :description,
                :price,
                :image_url,
                :category,
                :subcategory,
                :sub_subcategory
            )
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
?><!DOCTYPE html><html>
<head>
    <title>Upload Product</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family:Arial;padding:20px;"><h2>Upload Product</h2><?php if ($message): ?><p style="
    background:#f0f0f0;
    padding:10px;
    border-radius:5px;
">
    <?= htmlspecialchars($message) ?>
</p>
<?php endif; ?><form method="POST" enctype="multipart/form-data"><input
    type="text"
    name="name"
    placeholder="Product Name"
    required
    style="width:100%;padding:10px;margin-bottom:10px;"
>

<input
    type="text"
    name="price"
    placeholder="Price"
    required
    style="width:100%;padding:10px;margin-bottom:10px;"
>

<textarea
    name="desc"
    placeholder="Description"
    required
    style="width:100%;padding:10px;margin-bottom:10px;height:120px;"
></textarea>

<select
    name="category"
    required
    style="width:100%;padding:10px;margin-bottom:10px;"
>
    <option value="">Select Category</option>
    <option value="Clothing">Clothing</option>
    <option value="Food & Beverages">Food & Beverages</option>
    <option value="Electronics">Electronics</option>
</select>

<select
    name="subcategory"
    required
    style="width:100%;padding:10px;margin-bottom:10px;"
>
    <option value="">Select Subcategory</option>

    <option value="Male">Male</option>
    <option value="Female">Female</option>

    <option value="Beverages">Beverages</option>
    <option value="Grains">Grains</option>
    <option value="Snacks">Snacks</option>

    <option value="Phones">Phones</option>
    <option value="Laptops">Laptops</option>
    <option value="Accessories">Accessories</option>
</select>

<select
    name="sub_subcategory"
    required
    style="width:100%;padding:10px;margin-bottom:10px;"
>
    <option value="">Select Type</option>

    <option value="Men's Wear">Men's Wear</option>
    <option value="Boys' Wear">Boys' Wear</option>
    <option value="Men's Shoes">Men's Shoes</option>
    <option value="Boys' Shoes">Boys' Shoes</option>

    <option value="Women's Wear">Women's Wear</option>
    <option value="Girls' Wear">Girls' Wear</option>
    <option value="Women's Shoes">Women's Shoes</option>
    <option value="Girls' Shoes">Girls' Shoes</option>

    <option value="Others">Others</option>
</select>

<input
    type="file"
    name="image"
    required
    style="margin-bottom:15px;"
>

<br>

<button
    type="submit"
    style="
        background:green;
        color:white;
        border:none;
        padding:12px 20px;
        border-radius:5px;
        cursor:pointer;
    "
>
    Upload Product
</button>

</form></body>
</html><


