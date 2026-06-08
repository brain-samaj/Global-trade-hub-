<?php
session_start();
require "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error = "Please fill all fields";
    } else {

        // 1. CHECK ADMIN FIRST (optional simple admin check)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin["password"])) {

            $_SESSION["user_id"] = $admin["id"];
            $_SESSION["role"] = "admin";

            header("Location: admin/dashboard.php");
            exit;
        }

        // 2. CHECK SELLER
        $stmt = $pdo->prepare("SELECT * FROM sellers WHERE email = ?");
        $stmt->execute([$email]);
        $seller = $stmt->fetch();

        if ($seller && password_verify($password, $seller["password"])) {

            $_SESSION["seller_id"] = $seller["id"];
            $_SESSION["role"] = "seller";

            header("Location: seller-dashboard.php");
            exit;
        }

        // 3. CHECK BUYER (NEW SYSTEM)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'buyer'");
        $stmt->execute([$email]);
        $buyer = $stmt->fetch();

        if ($buyer && password_verify($password, $buyer["password"])) {

            $_SESSION["user_id"] = $buyer["id"];
            $_SESSION["role"] = "buyer";

            header("Location: buyer-dashboard.php");
            exit;
        }

        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Global Trade Hub</title>
    <style>
        body{
            font-family:Arial;
            background:#f4f4f4;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .box{
            background:white;
            padding:30px;
            border-radius:10px;
            width:320px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        input{
            width:100%;
            padding:10px;
            margin:10px 0;
        }

        button{
            width:100%;
            padding:10px;
            background:#0d47a1;
            color:white;
            border:none;
            cursor:pointer;
        }

        .error{
            color:red;
            text-align:center;
        }
    </style>
</head>

<body>

<div class="box">

    <h2 style="text-align:center;">Login</h2>

    <?php if($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">

        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>

    </form>

</div>

</body>
</html>
