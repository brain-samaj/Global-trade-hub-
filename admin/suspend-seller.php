<?php

require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

$id = $_GET['id'] ?? null;

if ($id) {

    $stmt = $pdo->prepare("
        UPDATE sellers
        SET status='suspended'
        WHERE id=?
    ");

    $stmt->execute([$id]);
}

header("Location: manage-sellers.php");
exit;
