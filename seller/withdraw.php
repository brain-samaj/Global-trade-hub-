<?php
session_start();
require "../config/db.php";

$seller_id = $_SESSION["seller_id"] ?? null;

if (!$seller_id) {
    die("Access denied");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $amount = (float)$_POST["amount"];
    $bank = $_POST["bank_name"];
    $acct = $_POST["account_number"];
    $name = $_POST["account_name"];

    // check balance
    $stmt = $pdo->prepare("SELECT wallet_balance FROM sellers WHERE id = :id");
    $stmt->execute([":id" => $seller_id]);
    $seller = $stmt->fetch();

    if ($amount > $seller["wallet_balance"]) {
        die("Insufficient balance");
    }

    // create withdrawal request
    $stmt = $pdo->prepare("
        INSERT INTO withdrawals
        (seller_id, amount, bank_name, account_number, account_name)
        VALUES (:seller_id, :amount, :bank, :acct, :name)
    ");

    $stmt->execute([
        ":seller_id" => $seller_id,
        ":amount" => $amount,
        ":bank" => $bank,
        ":acct" => $acct,
        ":name" => $name
    ]);

    $message = "Withdrawal request submitted";
}
?>

<h2>Withdraw Funds</h2>

<?php if ($message) echo "<p>$message</p>"; ?>

<form method="POST">

    <input type="number" name="amount" placeholder="Amount" required><br><br>

    <input type="text" name="bank_name" placeholder="Bank Name" required><br><br>

    <input type="text" name="account_number" placeholder="Account Number" required><br><br>

    <input type="text" name="account_name" placeholder="Account Name" required><br><br>

    <button type="submit">Request Withdrawal</button>

</form>
