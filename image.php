<?php

require "config/db.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Image not found");
}

// Fetch image safely
$stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = :id");
$stmt->execute([":id" => $id]);

$product = $stmt->fetch();

if (!$product) {
    die("Image not found");
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Image View</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="
    margin:0;
    background:black;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    overflow:hidden;
">

<!-- BACK BUTTON -->
<a href="javascript:history.back()" style="
    position:fixed;
    top:15px;
    left:15px;
    background:white;
    color:black;
    padding:10px 15px;
    border-radius:5px;
    text-decoration:none;
    font-weight:bold;
    z-index:999;
">
    ← Back
</a>

<!-- FULL IMAGE -->
<img src="<?= htmlspecialchars($product['image_url']) ?>"
     style="
        max-width:95%;
        max-height:95%;
        object-fit:contain;
        border-radius:10px;
     ">

</body>
</html>
