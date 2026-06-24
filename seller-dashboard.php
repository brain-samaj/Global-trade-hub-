<?php
session_start();

require "config/db.php";
require "includes/auth.php";

checkSeller();

/*
|--------------------------------------------------------------------------
| FETCH SELLER INFO
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$_SESSION["user_id"]]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$seller) {
    exit("Seller not found");
}

/*
|--------------------------------------------------------------------------
| FETCH PRODUCTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM products
    WHERE seller_id = ?
    ORDER BY id DESC
");

$stmt->execute([$_SESSION["user_id"]]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| FETCH ORDERS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.name AS product_name,
        p.price
    FROM orders o
    JOIN products p ON o.product_id = p.id
    WHERE p.seller_id = ?
    ORDER BY o.id DESC
");

$stmt->execute([$_SESSION["user_id"]]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?><!DOCTYPE html><html>
<head>
<title>Seller Dashboard</title><meta name="viewport" content="width=device-width, initial-scale=1.0"><style>

body{
    font-family:Arial;
    background:#f5f5f5;
    padding:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

.btn{
    padding:10px 15px;
    border-radius:5px;
    text-decoration:none;
    color:white;
    display:inline-block;
    margin-right:10px;
    border:none;
}

.green{background:green;}
.blue{background:#007bff;}
.red{background:red;}
.orange{background:orange;}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:15px;
}

img{
    width:100%;
    height:180px;
    object-fit:cover;
}

.order-box{
    border:1px solid #ddd;
    border-radius:8px;
    padding:15px;
    margin-bottom:15px;
}

</style></head><body><div class="card"><h2>
    Welcome <?= htmlspecialchars($seller["name"]) ?>
</h2>

<p>
    Email:
    <?= htmlspecialchars($seller["email"]) ?>
</p>

<p>
    Wallet Balance:
    <strong>
        ₦<?= number_format((float)($seller["wallet_balance"] ?? 0),2) ?>
    </strong>
</p>

<a href="seller-upload-product.php" class="btn green">
    ➕ Upload Product
</a>

<a href="seller-withdraw.php" class="btn blue">
    💰 Withdraw
</a>

<a href="logout.php" class="btn red">
    Logout
</a>

</div><!-- PRODUCTS --><div class="card"><h3>Your Products</h3><div class="grid"><?php foreach ($products as $p): ?><div class="card"><img
src="<?= htmlspecialchars($p["image_url"]) ?>"
onerror="this.src='https://via.placeholder.com/300'"

«»

<h3><?= htmlspecialchars($p["name"]) ?></h3><p><?= htmlspecialchars($p["description"]) ?></p><b>
₦<?= number_format((float)$p["price"]) ?>
</b><br><br>

<a
href="edit-product.php?id=<?= $p["id"] ?>"
class="btn orange"

«»

Edit
</a>

<form
method="POST"
action="delete-product.php"
style="display:inline;"
>
<input
type="hidden"
name="id"
value="<?= $p["id"] ?>"
><button
class="btn red"
type="submit"

«»

Delete
</button>

</form></div><?php endforeach; ?></div></div><!-- ORDERS --><div class="card"><h3>Recent Orders</h3><?php if (empty($orders)): ?><p>No orders yet.</p><?php endif; ?><?php foreach ($orders as $o): ?><div class="order-box"><p>
<b>Order ID:</b>
#<?= $o["id"] ?>
</p><p>
<b>Product:</b>
<?= htmlspecialchars($o["product_name"]) ?>
</p><p>
<b>Buyer:</b>
<?= htmlspecialchars($o["customer_name"] ?? "N/A") ?>
</p><p>
<b>Email:</b>
<?= htmlspecialchars($o["email"] ?? "N/A") ?>
</p><p>
<b>Phone:</b>
<?= htmlspecialchars($o["phone"] ?? "N/A") ?>
</p><p>
<b>Delivery Address:</b><br>
<?= nl2br(htmlspecialchars($o["delivery_address"] ?? "Not Provided")) ?>
</p><p>
<b>Amount Paid:</b>
₦<?= number_format((float)$o["amount"]) ?>
</p><p>
<b>Delivery Fee:</b>
₦<?= number_format((float)($o["delivery_fee"] ?? 0)) ?>
</p><p>
<b>Seller Earnings:</b>
₦<?= number_format((float)($o["seller_earnings"] ?? 0)) ?>
</p><p>
<b>Status:</b>
<?= htmlspecialchars($o["status"]) ?>
</p><?php if (
    $o["status"] === "paid"
    && !$o["seller_confirmed_shipped"]
): ?><a
href="seller-ship-order.php?id=<?= $o["id"] ?>"
class="btn green"

«»

🚚 Mark As Shipped
</a>

<?php endif; ?><?php if ($o["seller_confirmed_shipped"]): ?><p style="color:green;">
✅ Shipment Confirmed
</p><?php endif; ?><?php if ($o["buyer_confirmed_delivery"]): ?><p style="color:green;">
✅ Buyer Confirmed Delivery
</p><?php endif; ?><?php if ($o["wallet_released"]): ?><p style="color:blue;">
💰 Funds Released To Wallet
</p><?php endif; ?></div><?php endforeach; ?></div></body>
</html>
