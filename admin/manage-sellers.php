<?php

require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

$stmt = $pdo->query("
    SELECT
        s.*,
        COUNT(p.id) AS total_products
    FROM sellers s
    LEFT JOIN products p
        ON s.id = p.seller_id
    GROUP BY s.id
    ORDER BY s.created_at DESC
");

$sellers = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Sellers</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

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

        .back{
            background:#333;
        }

        .card{
            background:white;
            padding:20px;
            margin-bottom:20px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,.1);
        }

        .approve{
            background:green;
        }

        .suspend{
            background:red;
        }

        .stats{
            margin-top:10px;
        }
    </style>
</head>

<body>

<div class="top">

    <h2>Manage Sellers</h2>

    <a href="dashboard.php" class="btn back">
        ← Dashboard
    </a>

</div>

<?php if (!$sellers): ?>

    <div class="card">
        No sellers registered yet.
    </div>

<?php endif; ?>

<?php foreach ($sellers as $seller): ?>

<div class="card">

    <h3><?= htmlspecialchars($seller['full_name']) ?></h3>

    <p><strong>Email:</strong> <?= htmlspecialchars($seller['email']) ?></p>

    <p><strong>Phone:</strong> <?= htmlspecialchars($seller['phone']) ?></p>

    <p><strong>Location:</strong> <?= htmlspecialchars($seller['location']) ?></p>

    <p><strong>NIN:</strong> <?= htmlspecialchars($seller['nin']) ?></p>

    <p><strong>Status:</strong> <?= ucfirst($seller['status']) ?></p>

    <div class="stats">

        <p>
            <strong>Total Products:</strong>
            <?= $seller['total_products'] ?>
        </p>

        <p>
            <strong>Wallet Balance:</strong>
            ₦<?= number_format($seller['wallet_balance'], 2) ?>
        </p>

        <p>
            <strong>Total Earned:</strong>
            ₦<?= number_format($seller['total_earned'], 2) ?>
        </p>

        <p>
            <strong>Total Withdrawn:</strong>
            ₦<?= number_format($seller['total_withdrawn'], 2) ?>
        </p>

    </div>

    <div style="margin-top:15px;">

        <?php if ($seller['status'] !== 'approved'): ?>

            <a
                href="approve-seller.php?id=<?= $seller['id'] ?>"
                class="btn approve"
            >
                Approve
            </a>

        <?php endif; ?>

        <?php if ($seller['status'] !== 'suspended'): ?>

            <a
                href="suspend-seller.php?id=<?= $seller['id'] ?>"
                class="btn suspend"
            >
                Suspend
            </a>

        <?php endif; ?>

    </div>

</div>

<?php endforeach; ?>

</body>
</html>
