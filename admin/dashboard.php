<?php
require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

// Fetch products safely
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h2>Admin Dashboard</h2>

<a href="upload.php">➕ Add Product</a> |
<a href="logout.php">Logout</a>

<hr>

<h3>All Products</h3>

<div style="
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
    gap:15px;
">

<?php foreach ($products as $p): ?>

<div style="
    border:1px solid #ccc;
    padding:10px;
    border-radius:10px;
    background:#fff;
">

    <!-- IMAGE -->
    <img src="<?= htmlspecialchars($p['image_url']) ?>"
         style="width:100%; height:150px; object-fit:cover; border-radius:8px;">

    <!-- NAME -->
    <h3><?= htmlspecialchars($p['name']) ?></h3>

    <!-- DESCRIPTION -->
    <p><?= htmlspecialchars($p['description']) ?></p>

    <!-- PRICE (FIX $$ ISSUE) -->
    <?php
        $price = str_replace('$', '', $p['price']);
    ?>
    <b>$<?= htmlspecialchars($price) ?></b>

    <!-- DELETE -->
    <form method="POST" action="delete.php" onsubmit="return confirm('Delete this product?');">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <button style="background:red;color:white;padding:5px 10px;border:none;border-radius:5px;margin-top:10px;">
            Delete
        </button>
    </form>

</div>

<?php endforeach; ?>

</div>

</body>
</html>
