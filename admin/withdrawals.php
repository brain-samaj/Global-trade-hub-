<?php
require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

if (isset($_GET["approve"])) {

    $id = $_GET["approve"];

    $withdrawal = $pdo->prepare("SELECT * FROM withdrawals WHERE id = :id");
    $withdrawal->execute([":id" => $id]);
    $w = $withdrawal->fetch();

    if ($w) {

        // deduct from seller wallet
        $pdo->prepare("
            UPDATE sellers
            SET wallet_balance = wallet_balance - :amt,
                total_withdrawn = total_withdrawn + :amt
            WHERE id = :id
        ")->execute([
            ":amt" => $w["amount"],
            ":id" => $w["seller_id"]
        ]);

        // mark paid
        $pdo->prepare("
            UPDATE withdrawals
            SET status = 'approved'
            WHERE id = :id
        ")->execute([":id" => $id]);
    }
}

$stmt = $pdo->query("
    SELECT w.*, s.full_name
    FROM withdrawals w
    JOIN sellers s ON w.seller_id = s.id
    ORDER BY w.id DESC
");

$withdrawals = $stmt->fetchAll();
?>

<h2>Withdrawal Requests</h2>

<table border="1" width="100%" cellpadding="10">

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
    <td><?= htmlspecialchars($w["full_name"]) ?></td>
    <td>₦<?= number_format($w["amount"]) ?></td>
    <td><?= htmlspecialchars($w["bank_name"]) ?></td>
    <td><?= htmlspecialchars($w["account_number"]) ?></td>
    <td><?= $w["status"] ?></td>

    <td>
        <?php if ($w["status"] === "pending"): ?>
            <a href="?approve=<?= $w["id"] ?>">Approve</a>
        <?php else: ?>
            Done
        <?php endif; ?>
    </td>
</tr>

<?php endforeach; ?>

</table>
