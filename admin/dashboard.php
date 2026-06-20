<?php
session_start();
require "../config/db.php";

/*
|--------------------------------------------------------------------------
| AUTH GUARD
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? null) !== "admin") {
    header("Location: ../login.php");
    exit();
}

include "includes/header.php";
include "includes/navbar.php";
?>

/*
|--------------------------------------------------------------------------
| SUMMARY (ORDERS + REVENUE)
|--------------------------------------------------------------------------
*/

$summary = $pdo->query("
    SELECT
        COUNT(*) AS total_orders,
        COALESCE(SUM(CAST(amount AS NUMERIC)), 0) AS total_revenue
    FROM orders
    WHERE status = 'paid'
")->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| PRODUCTS + ANALYTICS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        p.*,
        u.name AS seller_name,
        COUNT(o.id) AS total_sales,
        COALESCE(SUM(CAST(o.amount AS NUMERIC)), 0) AS revenue
    FROM products p
    LEFT JOIN users u ON p.seller_id = u.id
    LEFT JOIN orders o ON p.id = o.product_id AND o.status = 'paid'
    GROUP BY p.id, u.name
    ORDER BY p.id DESC
");

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<h2>Admin Dashboard (Marketplace Control)</h2>

<!-- TOP ACTIONS -->
<div style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;">

    <a href="upload.php" style="background:green; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
        ➕ Add Product
    </a>

    <a href="manage-sellers.php" style="background:#333; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
        👤 Manage Sellers
    </a>

    <a href="withdrawals.php" style="background:#0d47a1; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
        💰 Withdrawals
    </a>

    <a href="logout.php" style="background:red; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
        Logout
    </a>

</div>

<!-- SUMMARY -->
<div style="background:white; padding:20px; border-radius:10px; margin-bottom:20px;">

    <h3>Sales Summary</h3>

    <p><strong>Total Paid Orders:</strong>
        <?= (int)$summary["total_orders"] ?>
    </p>

    <p><strong>Total Revenue:</strong>
        ₦<?= number_format((float)$summary["total_revenue"]) ?>
    </p>

</div>

<!-- PRODUCTS -->
<h3>Marketplace Products</h3>

<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:15px;">

<?php foreach ($products as $p): ?>

<div style="background:white; padding:15px; border-radius:10px;">

    <img
        src="<?= htmlspecialchars($p['image_url'] ?? '') ?>"
        style="width:100%; height:200px; object-fit:cover; border-radius:8px;"
        onerror="this.src='https://via.placeholder.com/300'"
    >

    <h3><?= htmlspecialchars($p['name']) ?></h3>

    <p><?= htmlspecialchars($p['description']) ?></p>

    <h3>₦<?= number_format((float)$p['price']) ?></h3>

    <p><strong>Seller:</strong> <?= htmlspecialchars($p['seller_name'] ?? 'Unknown') ?></p>

    <p><strong>Units Sold:</strong> <?= (int)$p['total_sales'] ?></p>

    <p><strong>Revenue:</strong> ₦<?= number_format((float)$p['revenue']) ?></p>

    <!-- ACTIONS -->
    <div style="display:flex; gap:10px; margin-top:10px;">

        <a href="edit.php?id=<?= $p['id'] ?>"
           style="background:orange; color:white; padding:8px 12px; text-decoration:none; border-radius:5px;">
            Edit
        </a>

        <form method="POST" action="delete.php" onsubmit="return confirm('Delete this product?');">
            <input type="hidden" name="id" value="<?= $p['id'] ?>">

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
