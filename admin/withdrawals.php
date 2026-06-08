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
| APPROVE WITHDRAWAL
|--------------------------------------------------------------------------
*/

$message = "";

if (isset($_GET["approve"])) {

    $id = (int) $_GET["approve"];

    try {

        $withdrawal = $pdo->prepare("
            SELECT *
            FROM withdrawals
            WHERE id = :id
            LIMIT 1
        ");

        $withdrawal->execute([
            ":id" => $id
        ]);

        $w = $withdrawal->fetch(PDO::FETCH_ASSOC);

        if (!$w) {

            $message = "Withdrawal request not found.";

        } elseif ($w["status"] !== "pending") {

            $message = "This withdrawal has already been processed.";

        } else {

            /*
            |----------------------------------------------------------
            | DEDUCT SELLER WALLET
            |----------------------------------------------------------
            */

            $pdo->prepare("
                UPDATE sellers
                SET
                    wallet_balance = wallet_balance - :amt,
                    total_withdrawn = total_withdrawn + :amt
                WHERE id = :seller_id
            ")->execute([
                ":amt" => $w["amount"],
                ":seller_id" => $w["seller_id"]
            ]);

            /*
            |----------------------------------------------------------
            | MARK AS APPROVED
            |----------------------------------------------------------
            */

            $pdo->prepare("
                UPDATE withdrawals
                SET status = 'approved'
                WHERE id = :id
            ")->execute([
                ":id" => $id
            ]);

            $message = "Withdrawal approved successfully.";
        }

    } catch (Exception $e) {

        $message = "Error: " . $e->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| FETCH WITHDRAWALS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        w.*,
        COALESCE(u.name, 'Unknown Seller') AS seller_name
    FROM withdrawals w
    LEFT JOIN sellers s
        ON w.seller_id = s.id
    LEFT JOIN users u
        ON s.user_id = u.id
    ORDER BY w.id DESC
");

$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Withdrawal Requests</title>

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <style>

        body{
            font-family:Arial;
            background:#f5f5f5;
            padding:20px;
        }

        .top{
            margin-bottom:20px;
        }

        .btn{
            display:inline-block;
            padding:10px 15px;
            text-decoration:none;
            border-radius:5px;
            color:white;
        }

        .back{
            background:#333;
        }

        .approve{
            background:green;
        }

        .message{
            background:white;
            padding:15px;
            margin-bottom:20px;
            border-radius:8px;
        }

        table{
            width:100%;
            border-collapse:collapse;
            background:white;
        }

        th,
        td{
            border:1px solid #ddd;
            padding:12px;
            text-align:left;
        }

        th{
            background:#f0f0f0;
        }

    </style>
</head>

<body>

<div class="top">

    <h2>Withdrawal Requests</h2>

    <a href="dashboard.php" class="btn back">
        ← Dashboard
    </a>

</div>

<?php if ($message): ?>

    <div class="message">
        <?= htmlspecialchars($message) ?>
    </div>

<?php endif; ?>

<table>

<tr>
    <th>Seller</th>
    <th>Amount</th>
    <th>Bank</th>
    <th>Account Number</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php if (empty($withdrawals)): ?>

<tr>
    <td colspan="6">
        No withdrawal requests found.
    </td>
</tr>

<?php endif; ?>

<?php foreach ($withdrawals as $w): ?>

<tr>

    <td>
        <?= htmlspecialchars($w["seller_name"]) ?>
    </td>

    <td>
        ₦<?= number_format($w["amount"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($w["bank_name"]) ?>
    </td>

    <td>
        <?= htmlspecialchars($w["account_number"]) ?>
    </td>

    <td>
        <?= ucfirst($w["status"]) ?>
    </td>

    <td>

        <?php if ($w["status"] === "pending"): ?>

            <a
                href="?approve=<?= $w["id"] ?>"
                class="btn approve"
                onclick="return confirm('Approve this withdrawal?')"
            >
                Approve
            </a>

        <?php else: ?>

            Done

        <?php endif; ?>

    </td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>
