<?php
require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();
?>

<h2>Admin Dashboard</h2>

<a href="upload.php">➕ Add Product</a> | 
<a href="logout.php">Logout</a>

<hr>

<h3>All Products</h3>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:15px;">

<?php foreach ($products as $p): ?>

<div style="border:1px solid #ccc;padding:10px;border-radius:10px;">

    <img src="<?= $p['image_url'] ?>" style="width:100%;border-radius:10px;">

    <h3><?= htmlspecialchars($p['name']) ?></h3>
    <p><?= htmlspecialchars($p['description']) ?></p>
    <b><?= htmlspecialchars($p['price']) ?></b>

    <form method="POST" action="delete.php">
        <input type="hidden" name="id" value="<?= $p['id'] ?>">
        <button style="background:red;color:white;padding:5px;border:none;margin-top:10px;">
            Delete
        </button>
    </form>

</div>

<?php endforeach; ?>

</div>
