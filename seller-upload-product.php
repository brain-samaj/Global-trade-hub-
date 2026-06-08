<?php
session_start();

require "config/db.php";
require "config/cloudinary.php";

/*
|--------------------------------------------------
| SELLER AUTH GUARD
|--------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "seller") {
    exit("Access denied: Sellers only.");
}

$seller_id = $_SESSION["user_id"];

/*
|--------------------------------------------------
| VERIFY SELLER EXISTS
|--------------------------------------------------
*/

$checkSeller = $pdo->prepare("
    SELECT id, name
    FROM users
    WHERE id = ? AND role = 'seller'
");

$checkSeller->execute([$seller_id]);

$seller = $checkSeller->fetch(PDO::FETCH_ASSOC);

if (!$seller) {
    exit("Seller account not found.");
}

$message = "";

/*
|--------------------------------------------------
| HANDLE PRODUCT UPLOAD
|--------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        if (
            empty($_POST["name"]) ||
            empty($_POST["price"]) ||
            empty($_POST["description"]) ||
            empty($_POST["category"])
        ) {
            throw new Exception("All required fields are needed.");
        }

        if (
            !isset($_FILES["image"]) ||
            $_FILES["image"]["error"] !== UPLOAD_ERR_OK
        ) {
            throw new Exception("Image upload failed.");
        }

        /*
        |------------------------------------------
        | UPLOAD IMAGE TO CLOUDINARY
        |------------------------------------------
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
        |------------------------------------------
        | CLEAN PRICE
        |------------------------------------------
        */

        $price = preg_replace(
            '/[^0-9]/',
            '',
            $_POST["price"]
        );

        /*
        |------------------------------------------
        | OPTIONAL FIELDS
        |------------------------------------------
        */

        $subcategory = trim($_POST["subcategory"] ?? "");
        $subSubcategory = trim($_POST["sub_subcategory"] ?? "");

        /*
        |------------------------------------------
        | INSERT PRODUCT
        |------------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO products (
                name,
                description,
                price,
                image_url,
                category,
                subcategory,
                sub_subcategory,
                seller_id
            )
            VALUES (
                :name,
                :description,
                :price,
                :image_url,
                :category,
                :subcategory,
                :sub_subcategory,
                :seller_id
            )
        ");

        $stmt->execute([
            ":name" => trim($_POST["name"]),
            ":description" => trim($_POST["description"]),
            ":price" => $price,
            ":image_url" => $imageUrl,
            ":category" => trim($_POST["category"]),
            ":subcategory" => $subcategory,
            ":sub_subcategory" => $subSubcategory,
            ":seller_id" => $seller_id
        ]);

        header("Location: seller-dashboard.php?uploaded=1");
        exit();

    } catch (Exception $e) {

        $message = "ERROR: " . $e->getMessage();

    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Upload Product</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

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
            width:100%;
            background:green;
            color:white;
            border:none;
            padding:12px;
            border-radius:5px;
            cursor:pointer;
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
        }

    </style>
</head>

<body>

<div class="container">

    <h2>Upload Product</h2>

    <div class="actions">
        <a href="seller-dashboard.php">← Back to Dashboard</a>
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
            name="description"
            placeholder="Product Description"
            required
        ></textarea>

        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Clothing">Clothing</option>
            <option value="Food & Beverages">Food & Beverages</option>
            <option value="Electronics">Electronics</option>
            <option value="Services">Services</option>
        </select>

        <input
            type="text"
            name="subcategory"
            placeholder="Subcategory (optional)"
        >

        <input
            type="text"
            name="sub_subcategory"
            placeholder="Type (optional)"
        >

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
