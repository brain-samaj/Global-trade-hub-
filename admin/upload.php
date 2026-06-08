<?php
session_start();

require "../config/db.php";
require "../config/cloudinary.php";

/*
|--------------------------------------------------------------------------
| ADMIN AUTH GUARD
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    exit("Access denied: Admins only.");
}

$message = "";

if (isset($_GET["success"])) {
    $message = "Product uploaded successfully!";
}

/*
|--------------------------------------------------------------------------
| HANDLE PRODUCT UPLOAD
|--------------------------------------------------------------------------
*/

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
            throw new Exception("All fields are required.");
        }

        if (
            !isset($_FILES["image"]) ||
            $_FILES["image"]["error"] !== UPLOAD_ERR_OK
        ) {
            throw new Exception("Image upload failed.");
        }

        /*
        |--------------------------------------------------------------------------
        | UPLOAD TO CLOUDINARY
        |--------------------------------------------------------------------------
        */

        $uploadResult = $cloudinary->uploadApi()->upload(
            $_FILES["image"]["tmp_name"],
            [
                "folder" => "global_trade_products"
            ]
        );

        if (!isset($uploadResult["secure_url"])) {
            throw new Exception("Cloudinary upload failed.");
        }

        $imageUrl = $uploadResult["secure_url"];

        /*
        |--------------------------------------------------------------------------
        | CLEAN PRICE
        |--------------------------------------------------------------------------
        */

        $price = (int) preg_replace(
            '/[^0-9]/',
            '',
            $_POST["price"]
        );

        /*
        |--------------------------------------------------------------------------
        | INSERT PRODUCT
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO products (
                name,
                description,
                price,
                image_url,
                category,
                subcategory,
                sub_subcategory
            )
            VALUES (
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

exit();header("Location: dashboard.php?uploaded=1");
exit();
    } catch (Exception $e) {

        $message = "ERROR: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Upload Product</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>

        body{
            font-family:Arial;
            background:#f4f4f4;
            padding:20px;
        }

        .container{
            max-width:700px;
            margin:auto;
            background:white;
            padding:25px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        h2{
            text-align:center;
        }

        input,
        textarea,
        select{
            width:100%;
            padding:12px;
            margin-bottom:15px;
            box-sizing:border-box;
        }

        textarea{
            height:120px;
        }

        button{
            background:green;
            color:white;
            border:none;
            padding:12px 20px;
            border-radius:5px;
            cursor:pointer;
            width:100%;
        }

        .message{
            background:#f0f0f0;
            padding:12px;
            border-radius:5px;
            margin-bottom:20px;
        }

        .actions{
            margin-bottom:20px;
        }

        .actions a{
            text-decoration:none;
            color:white;
            background:#0d47a1;
            padding:10px 15px;
            border-radius:5px;
            margin-right:10px;
        }

    </style>
</head>

<body>

<div class="container">

    <h2>Upload Product</h2>

    <div class="actions">
        <a href="dashboard.php">Dashboard</a>
        <a href="../index.php">Marketplace</a>
    </div>

    <?php if ($message): ?>
        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="text"
            name="name"
            placeholder="Product Name"
            required
        >

        <input
            type="text"
            name="price"
            placeholder="Price"
            required
        >

        <textarea
            name="desc"
            placeholder="Product Description"
            required
        ></textarea>

        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Clothing">Clothing</option>
            <option value="Food & Beverages">Food & Beverages</option>
            <option value="Electronics">Electronics</option>
        </select>

        <select name="subcategory" required>
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

        <select name="sub_subcategory" required>

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
            accept="image/*"
            required
        >

        <button type="submit">
            Upload Product
        </button>

    </form>

</div>

</body>
</html>
