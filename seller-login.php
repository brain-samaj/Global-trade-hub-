<?php
require "config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM sellers WHERE email = :email");
    $stmt->execute([":email" => $email]);
    $seller = $stmt->fetch();

    if ($seller && password_verify($password, $seller["password"])) {

        session_start();
        $_SESSION["seller_id"] = $seller["id"];

        header("Location: seller-dashboard.php");
        exit;

    } else {
        echo "Invalid login";
    }
}
?>

<h2>Seller Login</h2>

<form method="POST">

    <input type="email" name="email" placeholder="Email" required><br><br>

    <input type="password" name="password" placeholder="Password" required><br><br>

    <button type="submit">Login</button>

</form>
