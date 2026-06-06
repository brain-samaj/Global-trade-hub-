<?php
require "config/db.php";

$email = $_GET["email"] ?? null;

if (!$email) {
    die("Invalid request");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        UPDATE sellers
        SET password = :password, status = 'active'
        WHERE email = :email
    ");

    $stmt->execute([
        ":password" => $password,
        ":email" => $email
    ]);

    echo "Account created successfully. You can now login.";
    exit;
}
?>

<h2>Set Your Password</h2>

<form method="POST">

    <input type="password" name="password" placeholder="Create Password" required><br><br>

    <button type="submit">Finish Setup</button>

</form>
