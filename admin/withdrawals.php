<?php
session_start();
require "../config/db.php";

/*
|------------------------------------------
| ADMIN AUTH GUARD (FIXED)
|------------------------------------------
*/

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: ../login.php");
    exit();
}

if ($_SESSION["role"] !== "admin") {
    exit("Access denied: Admins only.");
}

/*
|------------------------------------------
| APPROVE WITHDRAWAL
|------------------------------------------
*/

$message = "";

if (isset($_GET["approve"])) {

    $id = (int) $_GET["approve"];

    try {

        $stmt = $pdo->prepare("
            SELECT * FROM withdrawals WHERE id = :id LIMIT 1
        ");
        $stmt->execute([":id" => $id]);
        $w = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$w) {
            $message = "Withdrawal not found";
        }
        elseif ($w["status"] !== "pending") {
            $message = "Already processed";
        }
        else {

            /*
            |--------------------------------------
            | UPDATE WITHDRAWAL STATUS FIRST
            |--------------------------------------
            */

            $pdo->prepare("
                UPDATE withdrawals
                SET status = 'approved'
                WHERE id = :id
            ")->execute([":id" => $id]);

            /*
            |--------------------------------------
            | UPDATE SELLER WALLET SAFELY
            |--------------------------------------
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

            $message = "Withdrawal approved successfully";
        }

    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
    }
}

/*
|------------------------------------------
| FETCH WITHDRAWALS (FIXED JOIN)
|------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        w.*,
        COALESCE(u.name, 'Unknown Seller') AS seller_name
    FROM withdrawals w
    LEFT JOIN users u ON w.seller_id = u.id
    ORDER BY w.id DESC
");

$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Withdrawal Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body{font-family:Arial;background:#f5f5f5;padding:20px;}
table{width:100%;background:white;border-collapse:collapse;}
th,td{padding:10px;border:1px solid #ddd;}
th{background:#eee;}
.btn{padding:6px 10px;background:green;color:white;text-decoration:none;border-radius:4px;}
</style>

</head>

<body>

<h2>Withdrawal Requests</h2>

<?php if ($message): ?>
<p style="background:#fff;padding:10px;">
    <?= htmlspecialchars($message) ?>
</p>
<?php endif; ?>

<table>

<tr>
    <th>Seller</th>
    <th>Amount</th>
    <th>Bank</th>
    <th>Account</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php foreach ($withdrawals as $w): ?>

<tr>
    <td><?= htmlspecialchars($w["seller_name"]) ?></td>
    <td>₦<?= number_format($w["amount"]) ?></td>
    <td><?= htmlspecialchars($w["bank_name"]) ?></td>
    <td><?= htmlspecialchars($w["account_number"]) ?></td>
    <td><?= ucfirst($w["status"]) ?></td>

    <td>
        <?php if ($w["status"] === "pending"): ?>
            <a class="btn" href="?approve=<?= $w["id"] ?>">Approve</a>
        <?php else: ?>
            Done
        <?php endif; ?>
    </td>
</tr>

<?php endforeach; ?>

</table>

</body>
</html>
