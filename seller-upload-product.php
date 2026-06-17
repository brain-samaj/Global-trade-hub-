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

‎      /*
‎      |------------------------------------------------>
‎      | HANDLE PRODUCT UPLOAD
‎      |------------------------------------------------>
‎      */
‎
‎       if ($_SERVER["REQUEST_METHOD"] === "POST") {
‎
‎        try {
‎
‎        if (
‎            empty($_POST["name"]) ||
‎            empty($_POST["price"]) ||
‎            empty($_POST["desc"]) ||
‎            empty($_POST["category"]) ||
‎            empty($_POST["subcategory"]) ||
‎            empty($_POST["sub_subcategory"])
‎        ) {
‎            throw new Exception("All fields are r>
‎        }
‎
‎        if (
‎            !isset($_FILES["image"]) ||
‎            $_FILES["image"]["error"] !== UPLOAD_>
‎        ) {
‎            throw new Exception("Image upload fai>
‎        }
‎
‎        /*
‎        |---------------------------------------->
‎        | UPLOAD TO CLOUDINARY
‎        |---------------------------------------->
‎        */
‎
‎        $uploadResult = $cloudinary->uploadApi()->
‎            $_FILES["image"]["tmp_name"],
‎            [
‎                "folder" => "global_trade_product>
‎            ]
‎        );
‎
‎        if (!isset($uploadResult["secure_url"])) {
‎            throw new Exception("Cloudinary uploa>
‎        }
‎
‎        $imageUrl = $uploadResult["secure_url"];
‎
‎        /*
‎        |---------------------------------------->
‎        | CLEAN PRICE
‎        |---------------------------------------->
‎        */
‎
‎        $price = (int) preg_replace(
‎            '/[^0-9]/',
‎            '',
‎            $_POST["price"]
‎        );
‎
‎        /*
‎        |---------------------------------------->
‎        | INSERT PRODUCT
‎        |---------------------------------------->
‎        */
‎
‎        $stmt = $pdo->prepare("
‎            INSERT INTO products (
‎                name,
‎                description,
‎                price,
‎                image_url,
‎                category,
‎                subcategory,
‎                sub_subcategory
‎            )
‎            VALUES (
‎                :name,
‎                :description,
‎                :price,
‎                :image_url,
‎                :category,
‎                :subcategory,
‎                :sub_subcategory
‎                :seller_id
‎            )
‎        ");
‎
‎        $stmt->execute([
‎            ":name" => trim($_POST["name"]),
‎            ":description" => trim($_POST["desc"]>
‎            ":price" => $price,
‎            ":image_url" => $imageUrl,
‎            ":category" => $_POST["category"],
‎            ":subcategory" => $_POST["subcategory>
‎            ":sub_subcategory" => $_POST["sub_sub>
‎            ":seller_id" => $seller_id
‎        ]);
‎
‎exit();header("Location: seller-dashboard.php?uploaded=1>
‎exit();
‎    } catch (Exception $e) {
‎
‎        $message = "ERROR: " . $e->getMessage();
‎    }
‎}
‎?>
‎
‎<!DOCTYPE html>
‎<html>
‎<head>
‎    <title>Seller Upload Product</title>
‎
‎    <meta name="viewport" content="width=device-w>
‎
‎    <style>
‎
‎        body{
‎            font-family:Arial;
‎            background:#f4f4f4;
‎            padding:20px;
‎        }
‎
‎        .container{
‎            max-width:700px;
‎            margin:auto;
‎            background:white;
‎            padding:25px;
‎            border-radius:10px;
‎            box-shadow:0 0 10px rgba(0,0,0,0.1);
‎        }
‎
‎        h2{
‎            text-align:center;
‎        }
‎
‎        input,
‎        textarea,
‎        select{
‎            width:100%;
‎            padding:12px;
‎            margin-bottom:15px;
‎            box-sizing:border-box;
‎        }
‎
‎        textarea{
‎            height:120px;
‎        }
‎
‎        button{
‎            background:green;
‎            color:white;
‎            border:none;
‎            padding:12px 20px;
‎            border-radius:5px;
‎            cursor:pointer;
‎            width:100%;
‎        }
‎
‎        .message{
‎            background:#f0f0f0;
‎            padding:12px;
‎            border-radius:5px;
‎            margin-bottom:20px;
‎        }
‎
‎        .actions{
‎            margin-bottom:20px;
‎        }
‎
‎        .actions a{
‎            text-decoration:none;
‎            color:white;
‎            background:#0d47a1;
‎            padding:10px 15px;
‎            border-radius:5px;
‎            margin-right:10px;
‎        }
‎
‎    </style>
‎</head>
‎
‎<body>
‎
‎<div class="container">
‎
‎    <h2>Upload Product</h2>
‎   <div class="actions">
‎        <a href="seller-dashboard.php">Dashboard</a>
‎        <a href="../index.php">Marketplace</a>
‎    </div>
‎
‎    <?php if ($message): ?>
‎        <div class="message">
‎            <?= htmlspecialchars($message) ?>
‎        </div>
‎    <?php endif; ?>
‎
‎    <form method="POST" enctype="multipart/form-d>
‎
‎        <input
‎            type="text"
‎            name="name"
‎            placeholder="Product Name"
‎            required
‎        >
‎
‎        <input
‎            type="text"
‎            name="price"
‎            placeholder="Price"
‎            required
‎        >
‎
‎        <textarea
‎            name="desc"
‎            placeholder="Product Description"
‎            required
‎        ></textarea>
‎
‎        <select name="category" required>
‎            <option value="">Select Category</opt>
‎            <option value="Clothing">Clothing</op>
‎            <option value="Food & Beverages">Food>
‎            <option value="Electronics">Electroni>
‎        </select>
‎
‎        <select name="subcategory" required>
‎            <option value="">Select Subcategory</>
‎            <option value="Male">Male</option>
‎            <option value="Female">Female</option>
‎
‎            <option value="Beverages">Beverages</>
‎            <option value="Grains">Grains</option>
‎            <option value="Snacks">Snacks</option>
‎
‎            <option value="Phones">Phones</option>
‎            <option value="Laptops">Laptops</opti>
‎            <option value="Accessories">Accessori>
‎        </select>
‎
‎        <select name="sub_subcategory" required>
‎
‎            <option value="">Select Type</option>
‎
‎            <option value="Men's Wear">Men's Wear>
‎            <option value="Boys' Wear">Boys' Wear>
‎            <option value="Men's Shoes">Men's Sho>
‎            <option value="Boys' Shoes">Boys' Sho>
‎
‎            <option value="Women's Wear">Women's >
‎            <option value="Girls' Wear">Girls' We>
‎            <option value="Women's Shoes">Women's>
‎            <option value="Girls' Shoes">Girls' S>
‎
‎            <option value="Others">Others</option>
‎
‎        </select>
‎
‎        <input
‎            type="file"
‎            name="image"
‎            accept="image/*"
‎            required
‎        >
‎
‎        <button type="submit">
‎            Upload Product
‎        </button>
‎
‎    </form>
‎
‎</div>
‎
‎</body>
‎</html>
‎
