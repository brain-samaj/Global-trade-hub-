<?php
session_start();
require "config/db.php";

/*
|--------------------------------------------------------------------------
| AUTH CHECK
|--------------------------------------------------------------------------
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
|--------------------------------------------------------------------------
| FETCH SELLER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$seller_id]);
$seller = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$seller) {
    exit("Seller not found");
}

/*
|--------------------------------------------------------------------------
| NIGERIA BANK LIST
|--------------------------------------------------------------------------
*/

$banks = [
    "Access Bank",
    "GTBank",
    "Zenith Bank",
    "First Bank of Nigeria",
    "UBA",
    "Fidelity Bank",
    "Union Bank",
    "Sterling Bank",
    "Wema Bank",
    "Polaris Bank",
    "Ecobank",
    "FCMB",
    "Jaiz Bank",
    "Providus Bank",
    "Stanbic IBTC"
];

/*
|--------------------------------------------------------------------------
| HANDLE WITHDRAWAL
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $amount = (float) $_POST["amount"];
        $bank_name = trim($_POST["bank_name"]);
        $account_number = trim($_POST["account_number"]);

        if ($amount <= 0 || $bank_name === "" || $account_number === "") {
            throw new Exception("All fields are required");
        }

        if (!in_array($bank_name, $banks)) {
            throw new Exception("Invalid bank selected");
        }

        /*
        |--------------------------------------------------------------------------
        | WALLET CHECK
        |--------------------------------------------------------------------------
        */

        $balance = (float) ($seller["wallet_balance"] ?? 0);

        if ($amount > $balance) {
            throw new Exception("Insufficient balance");
        }

        /*
        |--------------------------------------------------------------------------
        | WITHDRAWAL FEE (0.25%)
        |--------------------------------------------------------------------------
        */

        $fee = $amount * 0.0025;
        $net = $amount - $fee;

        /*
        |--------------------------------------------------------------------------
        | INSERT WITHDRAWAL
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO withdrawals
            (seller_id, amount, fee, net_amount, bank_name, account_number, status)
            VALUES
            (:seller_id, :amount, :fee, :net_amount, :bank_name, :account_number, 'pending')
        ");

        $stmt->execute([
            ":seller_id" => $seller_id,
            ":amount" => $amount,
            ":fee" => $fee,
            ":net_amount" => $net,
            ":bank_name" => $bank_name,
            ":account_number" => $account_number
        ]);

        $message = "Withdrawal request submitted successfully";

    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| FETCH HISTORY
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM withdrawals
    WHERE seller_id = ?
    ORDER BY id DESC
");

$stmt->execute([$seller_id]);
$withdrawals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Withdraw</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            margin-bottom:20px;
        }

        input, select{
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
            background:white;
            border-collapse:collapse;
        }

        td, th{
            border:1px solid #ddd;
            padding:8px;
        }

        th{
            background:#eee;
        }

        .msg{
            padding:10px;
            background:#e6ffe6;
            margin-bottom:10px;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>Withdraw Funds</h2>

    <?php if ($message): ?>
        <div class="msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="number" name="amount" placeholder="Amount" required>

        <select name="bank_name" required>
            <option value="">Select Bank</option>
            <?php foreach ($banks as $b): ?>
                <option value="<?= $b ?>"><?= $b ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="account_number" placeholder="Account Number" required>

        <button type="submit">Request Withdrawal</button>

    </form>

</div>

<div class="box">

    <h3>Withdrawal History</h3>

    <table>
        <tr>
            <th>Amount</th>
            <th>Fee</th>
            <th>Net</th>
            <th>Bank</th>
            <th>Status</th>
        </tr>

        <?php foreach ($withdrawals as $w): ?>
        <tr>
            <td>₦<?= number_format($w["amount"]) ?></td>
            <td>₦<?= number_format($w["fee"] ?? 0) ?></td>
            <td>₦<?= number_format($w["net_amount"] ?? 0) ?></td>
            <td><?= htmlspecialchars($w["bank_name"]) ?></td>
            <td><?= ucfirst($w["status"]) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

</div>

</body>
</html>
