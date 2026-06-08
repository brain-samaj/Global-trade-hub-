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

/*
|--------------------------------------------------------------------------
| FETCH SELLER INFO (FROM users TABLE NOW)
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$seller_id]);
$seller = $stmt->fetch();

if (!$seller) {
    exit("Seller not found");
}

/*
|--------------------------------------------------------------------------
| FETCH SELLER PRODUCTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM products
    WHERE seller_id = ?
    ORDER BY id DESC
");

$stmt->execute([$seller_id]);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<h2>Welcome <?= htmlspecialchars($seller["name"] ?? "Seller") ?></h2>

<p>Email: <?= htmlspecialchars($seller["email"]) ?></p>

<!-- ACTION BUTTONS -->
<div style="margin:20px 0; display:flex; gap:10px; flex-wrap:wrap;">

    <a href="seller-upload-product.php"
       style="background:green; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
        ➕ Upload Product
    </a>

    <a href="seller-withdraw.php"
       style="background:#007bff; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
        💰 Withdraw
    </a>

    <a href="logout.php"
       style="background:red; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
        Logout
    </a>

</div>

<!-- PRODUCTS -->
<h3>Your Products</h3>

<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:20px;">

<?php foreach ($products as $p): ?>

<div style="background:white; padding:15px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1);">

    <img src="<?= htmlspecialchars($p["image_url"]) ?>"
         style="width:100%; height:180px; object-fit:cover; border-radius:10px;"
         onerror="this.src='https://via.placeholder.com/300'">

    <h3><?= htmlspecialchars($p["name"]) ?></h3>

    <p><?= htmlspecialchars($p["description"]) ?></p>

    <h3>₦<?= number_format((float)$p["price"]) ?></h3>

    <p><strong>Category:</strong> <?= htmlspecialchars($p["category"] ?? "N/A") ?></p>

    <!-- ACTIONS -->
    <div style="display:flex; gap:10px; margin-top:10px;">

        <a href="seller-edit-product.php?id=<?= $p["id"] ?>"
           style="background:orange; color:white; padding:8px 12px; text-decoration:none; border-radius:5px;">
            Edit
        </a>

        <form method="POST" action="seller-delete-product.php"
              onsubmit="return confirm('Delete this product?');">

            <input type="hidden" name="id" value="<?= $p["id"] ?>">

            <button type="submit"
                style="background:red; color:white; border:none; padding:8px 12px; border-radius:5px; cursor:pointer;">
                Delete
            </button>

        </form>

    </div>

</div>

<?php endforeach; ?>

</div>

</body>
</html>
