<?php
require "../config/db.php";
require "../includes/auth.php";

checkAdmin();

if ($_POST && isset($_POST["id"])) {

    $id = $_POST["id"];

    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    $stmt->execute([":id" => $id]);

    header("Location: dashboard.php");
    exit;
}
