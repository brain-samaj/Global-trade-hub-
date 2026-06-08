<?php
require "config/db.php";
session_start();

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } elseif ($user['role'] == 'seller') {
        header("Location: seller-dashboard.php");
    } else {
        header("Location: index.php");
    }

} else {
    echo "Invalid login details";
}
?>
