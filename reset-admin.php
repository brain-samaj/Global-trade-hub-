<?php
require "config/db.php";

$email = "brainsamaj2004@gmail.com";
$password = password_hash("Brain-samaj", PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    UPDATE users
    SET password = ?
    WHERE email = ?
");

$stmt->execute([$password, $email]);

echo "Admin password reset successful";
?>
