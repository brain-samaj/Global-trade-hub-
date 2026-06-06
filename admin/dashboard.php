<?php

require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

/*
|--------------------------------------------------------------------------
| SUMMARY (ORDERS + REVENUE)
|--------------------------------------------------------------------------
*/

$summary = $pdo->query("
    SELECT
        COUNT(*) AS total_orders,
        COALESCE(SUM(amount::NUMERIC), 0) AS total_revenue
    FROM orders
    WHERE status = 'paid'
")->fetch();

/*
|--------------------------------------------------------------------------
| PRODUCTS + SELLER + SALES ANALYTICS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        p.*,
        s.full_name AS seller_name,
        COUNT(o.id) AS total_sales,
        COALESCE(SUM(o.amount::NUMERIC), 0) AS total_revenue
    FROM products p
    LEFT JOIN sellers s ON p.seller_id = s.id
    LEFT JOIN orders o ON p.id = o.product_id AND o.status = 'paid'
    GROUP BY p.id, s.full_name
    ORDER BY p.id DESC
");

$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body style="font-family:Arial;background:#f5f5f5;margin:0;padding:20px;">

<h2>Admin Dashboard (Marketplace Control)</h2>

<!-- TOP ACTIONS -->
<div style="
    margin-bottom:20px;
    display:flex;
    flex-wrap:wrap;
    gap:10px;
">
  <a href="upload.php" style="
    background:green;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:5px;
">
    ➕ Add Product
</a>

<a href="manage-sellers.php" style="
    background:#333;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:5px;
">
    👤 Manage Sellers
</a>

<a href="logout.php" style="
    background:red;
    color:white;
    padding:10px 15px;
    text-decoration:none;
    border-radius:5px;
">
    Logout
</a>

</div>

<!-- SUMMARY -->
<div style="background:white;padding:20px;border-radius:10px;margin-bottom:20px;">

    <h3>Sales Summary</h3>

    <p><strong>Total Paid Orders:</strong> <?= (int)$summary['total_orders'] ?></p>

    <p><strong>Total Revenue:</strong> ₦<?= number_format((float)$summary['total_revenue']) ?></p>

</div>

<hr>

<h3>Marketplace Products</h3>

<div style="
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
">

<?php foreach ($products as $p): ?>

<div style="
    background:white;
    padding:15px;
    border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
">

<!-- IMAGE -->
<img
    src="<?= !empty($p['image_url']) ? trim($p['image_url']) : 'https://via.placeholder.com/400x220?text=No+Image' ?>"
    alt="<?= htmlspecialchars($p['name']) ?>"
    style="
        width:100%;
        height:220px;
        object-fit:cover;
        border-radius:10px;
        display:block;
    "
    loading="lazy"
    onerror="this.src='https://via.placeholder.com/400x220?text=Image+Unavailable';"
>

<h3><?= htmlspecialchars($p['name']) ?></h3>

<p><?= htmlspecialchars($p['description']) ?></p>

<h3>
    ₦<?= number_format((int)preg_replace('/[^0-9]/', '', $p['price'])) ?>
</h3>

<!--SELLER INFO -->
    <p>
        <strong>Seller:</strong>
        <?= htmlspecialchars($p['seller_name'] ?? 'Admin') ?>
    </p>

    <hr>

    <!-- STATS -->
    <p><strong>Units Sold:</strong> <?= (int)$p['total_sales'] ?></p>

    <p><strong>Revenue:</strong> ₦<?= number_format((float)$p['total_revenue']) ?></p>

    <!-- ACTIONS -->
    <div style="display:flex;gap:10px;margin-top:10px;">

        <a href="edit.php?id=<?= $p['id'] ?>" style="
            background:orange;
            color:white;
            padding:8px 12px;
            text-decoration:none;
            border-radius:5px;
        ">
            Edit
        </a>

        <form method="POST" action="delete.php" onsubmit="return confirm('Delete product?');">

            <input type="hidden" name="id" value="<?= $p['id'] ?>">

            <button type="submit" style="
                background:red;
                color:white;
                border:none;
                padding:8px 12px;
                border-radius:5px;
                cursor:pointer;
            ">
                Delete
            </button>

        </form>

    </div>

</div>

<?php endforeach; ?>

</div>

</body>
</html>
