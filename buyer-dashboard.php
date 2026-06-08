<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "buyer") {
    header("Location: login.php");
    exit;
}
?>

<h1>Welcome Buyer 👋</h1>

<p>This is your dashboard.</p>

<a href="index.php">Go to Marketplace</a>
