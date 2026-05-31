<?php

require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

/*
|--------------------------------------------------------------------------
| DASHBOARD SUMMARY
|--------------------------------------------------------------------------
*/

$summary = $pdo->query("
    SELECT
        COUNT(*) AS total_orders,
        COALESCE(SUM(amount), 0) AS total_revenue
    FROM orders
    WHERE status = 'paid'
")->fetch();

/*
|--------------------------------------------------------------------------
| PRODUCTS + SALES STATS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        p.*,
        COUNT(o.id) AS total_sales,
        COALESCE(SUM(o.amount), 0) AS total_revenue
    FROM products p
    LEFT JOIN orders o
        ON p.id = o.product_id
        AND o.status = 'paid'
    GROUP BY p.id
    ORDER BY p.id DESC
");

$products = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body style="
    font-family:Arial,sans-serif;
    background:#f5f5f5;
    margin:0;
    padding:20px;
">

<h2>Admin Dashboard</h2>

<div style="margin-bottom:20px;">

    <a href="upload.php" style="
        background:green;
        color:white;
        padding:10px 15px;
        text-decoration:none;
        border-radius:5px;
    ">
        ➕ Add Product
    </a>

    <a href="logout.php" style="
        background:red;
        color:white;
        padding:10px 15px;
        text-decoration:none;
        border-radius:5px;
        margin-left:10px;
    ">
        Logout
    </a>

</div>

<!-- SALES SUMMARY -->

<div style="
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:25px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
">

    <h3>Sales Summary</h3>

    <p>
        <strong>Total Paid Orders:</strong>
        <?= (int)$summary['total_orders'] ?>
    </p>

    <p>
        <strong>Total Revenue:</strong>
        ₦<?= number_format((float)$summary['total_revenue']) ?>
    </p>

</div>

<hr>

<h3>All Products</h3>

<div style="
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:20px;
">

<?php foreach ($products as $p): ?>

<div style="
    background:white;
    border-radius:10px;
    padding:15px;
    box-shadow:0 0 10px rgba(0,0,0,.1);
">

    <!-- PRODUCT IMAGE -->

    <img
        src="<?= htmlspecialchars($p['image_url']) ?>"
        style="
            width:100%;
            height:220px;
            object-fit:cover;
            border-radius:10px;
        "
    >

    <!-- PRODUCT NAME -->

    <h3><?= htmlspecialchars($p['name']) ?></h3>

    <!-- PRODUCT DESCRIPTION -->

    <p><?= htmlspecialchars($p['description']) ?></p>

    <!-- PRODUCT PRICE -->

    <?php
        $price = preg_replace('/[^0-9]/', '', $p['price']);
    ?>

    <h3>
        ₦<?= number_format((int)$price) ?>
    </h3>

    <hr>

    <!-- SALES STATS -->

    <p>
        <strong>Units Sold:</strong>
        <?= (int)$p['total_sales'] ?>
    </p>

    <p>
        <strong>Revenue Generated:</strong>
        ₦<?= number_format((float)$p['total_revenue']) ?>
    </p>

    <!-- ACTION BUTTONS -->

    <div style="
        display:flex;
        gap:10px;
        margin-top:15px;
    ">

        <!-- EDIT -->

        <a href="edit.php?id=<?= $p['id'] ?>"
           style="
                background:orange;
                color:white;
                padding:8px 12px;
                text-decoration:none;
                border-radius:5px;
                display:inline-block;
           ">
            Edit
        </a>

        <!-- DELETE -->

        <form
            method="POST"
            action="delete.php"
            onsubmit="return confirm('Delete this product?');"
        >

            <input
                type="hidden"
                name="id"
                value="<?= $p['id'] ?>"
            >

            <button
                type="submit"
                style="
                    background:red;
                    color:white;
                    border:none;
                    padding:8px 12px;
                    border-radius:5px;
                    cursor:pointer;
                "
            >
                Delete
            </button>

        </form>

    </div>

</div>

<?php endforeach; ?>

</div>

</body>
</html>
