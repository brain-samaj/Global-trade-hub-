<?php
session_start();

/*
|--------------------------------------------------------------------------
| AUTH GUARD (UNIFIED SYSTEM)
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"]) || !isset($_SESSION["role"])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION["role"] !== "buyer") {
    die("Access denied: Buyers only.");
}

require "config/db.php";

/*
|--------------------------------------------------------------------------
| OPTIONAL: FETCH USER INFO
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT name, email, profile_pic
    FROM users
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([":id" => $_SESSION["user_id"]]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buyer Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<div style="background:white; padding:20px; border-radius:10px; max-width:600px; margin:auto;">

    <h1>Welcome <?= htmlspecialchars($user["name"] ?? "Buyer") ?> 👋</h1>

    <p>Email: <?= htmlspecialchars($user["email"] ?? "") ?></p>

    <hr>

    <h3>Your Dashboard</h3>

    <p>You can browse products, place orders, and track purchases.</p>

    <div style="margin-top:20px; display:flex; gap:10px; flex-wrap:wrap;">

        <a href="index.php" style="background:#0d47a1; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
            🛒 Go to Marketplace
        </a>

        <a href="orders.php" style="background:#333; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
            📦 My Orders
        </a>

        <a href="logout.php" style="background:red; color:white; padding:10px 15px; text-decoration:none; border-radius:5px;">
            Logout
        </a>

    </div>

</div>

</body>
</html>
