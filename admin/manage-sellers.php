<?php
session_start();

require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

/*
|--------------------------------------------------------------------------
| FETCH SELLERS (USERS TABLE IS SOURCE OF TRUTH)
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        u.id AS user_id,
        u.name,
        u.email,
        u.phone,
        u.country,
        u.city,
        u.role,
        u.created_at,
        COALESCE(s.status, 'pending') AS status,
        COALESCE(s.wallet_balance, 0) AS wallet_balance,
        COALESCE(s.total_earned, 0) AS total_earned,
        COALESCE(s.total_withdrawn, 0) AS total_withdrawn,
        (
            SELECT COUNT(*)
            FROM products p
            WHERE p.seller_id = u.id
        ) AS total_products
    FROM users u
    LEFT JOIN sellers s
        ON s.user_id = u.id
    WHERE u.role = 'seller'
    ORDER BY u.id DESC
");

$sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Sellers</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body{
            font-family:Arial,sans-serif;
            background:#f5f5f5;
            padding:20px;
        }

        .top{
            margin-bottom:20px;
        }

        .btn{
            display:inline-block;
            padding:10px 15px;
            border-radius:5px;
            text-decoration:none;
            color:white;
        }

        .back{ background:#333; }
        .approve{ background:green; }
        .suspend{ background:red; }

        .card{
            background:white;
            padding:20px;
            margin-bottom:20px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,.1);
        }

        .stats{
            margin-top:15px;
        }
    </style>
</head>

<body>

<div class="top">
    <h2>Manage Sellers</h2>
    <a href="dashboard.php" class="btn back">← Dashboard</a>
</div>

<?php if (empty($sellers)): ?>
    <div class="card">
        No sellers registered yet.
    </div>
<?php endif; ?>

<?php foreach ($sellers as $seller): ?>

<div class="card">

    <h3><?= htmlspecialchars($seller["name"]) ?></h3>

    <p><strong>Email:</strong> <?= htmlspecialchars($seller["email"]) ?></p>

    <p><strong>Phone:</strong> <?= htmlspecialchars($seller["phone"] ?? 'N/A') ?></p>

    <p><strong>Location:</strong> <?= htmlspecialchars(($seller["city"] ?? '') . ' ' . ($seller["country"] ?? '')) ?></p>

    <p><strong>Role:</strong> <?= htmlspecialchars($seller["role"]) ?></p>

    <p><strong>Status:</strong> <?= htmlspecialchars($seller["status"]) ?></p>

    <div class="stats">

        <p><strong>Total Products:</strong> <?= (int)$seller["total_products"] ?></p>

        <p><strong>Wallet Balance:</strong> ₦<?= number_format((float)$seller["wallet_balance"]) ?></p>

        <p><strong>Total Earned:</strong> ₦<?= number_format((float)$seller["total_earned"]) ?></p>

        <p><strong>Total Withdrawn:</strong> ₦<?= number_format((float)$seller["total_withdrawn"]) ?></p>

    </div>

    <div style="margin-top:15px;">

        <?php if (($seller["status"] ?? "pending") !== "approved"): ?>
            <a href="approve-seller.php?id=<?= $seller["user_id"] ?>" class="btn approve">
                Approve
            </a>
        <?php endif; ?>

        <?php if (($seller["status"] ?? "pending") !== "suspended"): ?>
            <a href="suspend-seller.php?id=<?= $seller["user_id"] ?>" class="btn suspend"
               onclick="return confirm('Suspend this seller?')">
                Suspend
            </a>
        <?php endif; ?>

    </div>

</div>

<?php endforeach; ?>

</body>
</html>
