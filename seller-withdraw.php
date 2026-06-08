<?php
session_start();
require "config/db.php";

/*
|------------------------------------------
| AUTH CHECK (SELLER ONLY)
|------------------------------------------
*/

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION["role"] !== "seller") {
    exit("Access denied: Sellers only.");
}

$seller_id = $_SESSION["user_id"];
$message = "";

/*
|------------------------------------------
| GET SELLER INFO
|------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT * FROM users WHERE id = :id AND role = 'seller'
");
$stmt->execute([":id" => $seller_id]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$seller) {
    exit("Seller not found");
}

/*
|------------------------------------------
| HANDLE WITHDRAW REQUEST
|------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $amount = (int) $_POST["amount"];
        $bank_name = trim($_POST["bank_name"]);
        $account_number = trim($_POST["account_number"]);

        if ($amount <= 0 || $bank_name === "" || $account_number === "") {
            throw new Exception("All fields are required");
        }

        /*
        |--------------------------------------
        | OPTIONAL: CHECK BALANCE SAFETY
        |--------------------------------------
        */

        $balance = (float) ($seller["wallet_balance"] ?? 0);

        if ($amount > $balance) {
            throw new Exception("Insufficient balance");
        }

        /*
        |--------------------------------------
        | INSERT WITHDRAW REQUEST
        |--------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO withdrawals
            (seller_id, amount, bank_name, account_number, status)
            VALUES
            (:seller_id, :amount, :bank_name, :account_number, 'pending')
        ");

        $stmt->execute([
            ":seller_id" => $seller_id,
            ":amount" => $amount,
            ":bank_name" => $bank_name,
            ":account_number" => $account_number
        ]);

        $message = "Withdrawal request submitted successfully";

    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

/*
|------------------------------------------
| FETCH WITHDRAWAL HISTORY
|------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM withdrawals
    WHERE seller_id = :id
    ORDER BY id DESC
");

$stmt->execute([":id" => $seller_id]);

$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Withdraw</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body{
            font-family:Arial;
            background:#f5f5f5;
            padding:20px;
        }

        .box{
            max-width:500px;
            margin:auto;
            background:white;
            padding:20px;
            border-radius:10px;
        }

        input{
            width:100%;
            padding:10px;
            margin-bottom:10px;
        }

        button{
            width:100%;
            padding:10px;
            background:green;
            color:white;
            border:none;
            cursor:pointer;
        }

        table{
            width:100%;
            margin-top:20px;
            background:white;
            border-collapse:collapse;
        }

        td,th{
            border:1px solid #ddd;
            padding:8px;
        }

        th{
            background:#eee;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>Withdraw Funds</h2>

    <?php if ($message): ?>
        <p style="color:green;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <input type="number" name="amount" placeholder="Amount" required>

        <input type="text" name="bank_name" placeholder="Bank Name" required>

        <input type="text" name="account_number" placeholder="Account Number" required>

        <button type="submit">Request Withdrawal</button>

    </form>

</div>

<!-- HISTORY -->
<div class="box">

    <h3>Withdrawal History</h3>

    <table>

        <tr>
            <th>Amount</th>
            <th>Bank</th>
            <th>Account</th>
            <th>Status</th>
        </tr>

        <?php foreach ($withdrawals as $w): ?>
        <tr>
            <td>₦<?= number_format($w["amount"]) ?></td>
            <td><?= htmlspecialchars($w["bank_name"]) ?></td>
            <td><?= htmlspecialchars($w["account_number"]) ?></td>
            <td><?= ucfirst($w["status"]) ?></td>
        </tr>
        <?php endforeach; ?>

    </table>

</div>

</body>
</html>
