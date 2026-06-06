<?php
session_start();
require "config/db.php";

if (!isset($_SESSION["seller_id"])) {
    die("Access denied");
}

$seller_id = $_SESSION["seller_id"];

// get seller info
$stmt = $pdo->prepare("SELECT * FROM sellers WHERE id = :id");
$stmt->execute([":id" => $seller_id]);
$seller = $stmt->fetch();

echo "<h2>Welcome " . htmlspecialchars($seller["full_name"]) . "</h2>";
echo "<p>Location: " . htmlspecialchars($seller["location"]) . "</p>";
echo "<p>Status: " . htmlspecialchars($seller["status"]) . "</p>";
?>

<a href="seller-upload-product.php">Upload Product</a>
