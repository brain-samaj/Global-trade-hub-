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

        // FETCH USER
        $stmt = $pdo->prepare("
            SELECT * FROM users
            WHERE email = :email
            LIMIT 1
        ");
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // VALIDATE USER
        if (!$user) {
            $error = "Invalid email or password";
        } elseif (!password_verify($password, $user["password"])) {
            $error = "Invalid email or password";
        } else {

            // SESSION
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            // ROLE ROUTING
            if ($user["role"] === "admin") {
                header("Location: admin/dashboard.php");
                exit;
            }

            if ($user["role"] === "seller") {
                header("Location: seller-dashboard.php");
                exit;
            }

            // default = buyer
            header("Location: buyer-dashboard.php");
            exit;
        }
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
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">

        <input type="email" name="email" placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>

    </form>

</div>

</body>
</html>
