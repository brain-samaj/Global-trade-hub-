<?php
session_start();

require "../config/db.php";

/*
|--------------------------------------------------------------------------
| ADMIN AUTH GUARD
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    exit("Access denied: Admins only.");
}

/*
|--------------------------------------------------------------------------
| FETCH SELLERS + PRODUCT STATS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        s.*,
        u.name,
        u.email,
        COUNT(p.id) AS total_products
    FROM sellers s
    INNER JOIN users u
        ON s.user_id = u.id
    LEFT JOIN products p
        ON s.id = p.seller_id
    GROUP BY
        s.id,
        u.name,
        u.email
    ORDER BY s.id DESC
");

$sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Sellers</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

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

        .approve{
            background:green;
        }

        .suspend{
            background:red;
        }

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

    <a href="dashboard.php" class="btn back">
        ← Dashboard
    </a>

</div>

<?php if (empty($sellers)): ?>

    <div class="card">
        No sellers registered yet.
    </div>

<?php endif; ?>


<?php foreach ($sellers as $seller): ?>

<div class="card">

    <h3>
        <?= htmlspecialchars($seller["name"]) ?>
    </h3>

    <p>
        <strong>Email:</strong>
        <?= htmlspecialchars($seller["email"]) ?>
    </p>

    <p>
        <strong>Phone:</strong>
        <?= htmlspecialchars($seller["phone"] ?? "") ?>
    </p>

    <p>
        <strong>Location:</strong>
        <?= htmlspecialchars($seller["location"] ?? "") ?>
    </p>

    <p>
        <strong>NIN:</strong>
        <?= htmlspecialchars($seller["nin"] ?? "") ?>
    </p>

    <p>
        <strong>Status:</strong>
        <?= ucfirst($seller["status"] ?? "pending") ?>
    </p>

    <div class="stats">

        <p>
            <strong>Total Products:</strong>
            <?= $seller["total_products"] ?>
        </p>

        <p>
            <strong>Wallet Balance:</strong>
            ₦<?= number_format($seller["wallet_balance"] ?? 0) ?>
        </p>

        <p>
            <strong>Total Earned:</strong>
            ₦<?= number_format($seller["total_earned"] ?? 0) ?>
        </p>

        <p>
            <strong>Total Withdrawn:</strong>
            ₦<?= number_format($seller["total_withdrawn"] ?? 0) ?>
        </p>

    </div>

    <div style="margin-top:15px;">

        <?php if (($seller["status"] ?? "") !== "approved"): ?>

            <a
                href="approve-seller.php?id=<?= $seller["id"] ?>"
                class="btn approve"
            >
                Approve
            </a>

        <?php endif; ?>


        <?php if (($seller["status"] ?? "") !== "suspended"): ?>

            <a
                href="suspend-seller.php?id=<?= $seller["id"] ?>"
                class="btn suspend"
                onclick="return confirm('Suspend this seller?')"
            >
                Suspend
            </a>

        <?php endif; ?>

    </div>

</div>

<?php endforeach; ?>

</body>
</html>
