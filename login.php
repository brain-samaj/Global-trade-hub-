<?php
session_start();
require "config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $error = "Please fill all fields";

    } else {

        $stmt = $pdo->prepare("
            SELECT id, name, email, password, role
            FROM users
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ":email" => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user["password"])) {

            $error = "Invalid email or password";

        } elseif (empty($user["role"])) {

            $error = "Account role not assigned";

        } else {

            /*
            |-----------------------------------------
            | UNIFIED SESSION
            |-----------------------------------------
            */

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            /*
            |-----------------------------------------
            | ROLE ROUTING
            |-----------------------------------------
            */

            if ($user["role"] === "admin") {

                header("Location: admin/dashboard.php");
                exit;
            }

            if ($user["role"] === "seller") {

                header("Location: seller-dashboard.php");
                exit;
            }

            if ($user["role"] === "buyer") {

                header("Location: buyer-dashboard.php");
                exit;
            }

            $error = "Invalid account role.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Login - Global Trade Hub</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>

        body{
            font-family:Arial;
            background:#f4f4f4;
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
            margin:0;
        }

        .box{
            background:white;
            padding:30px;
            border-radius:10px;
            width:320px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        h2{
            text-align:center;
            margin-bottom:20px;
        }

        input{
            width:100%;
            padding:10px;
            margin:10px 0;
            box-sizing:border-box;
        }

        button{
            width:100%;
            padding:12px;
            background:#0d47a1;
            color:white;
            border:none;
            cursor:pointer;
            border-radius:5px;
        }

        button:hover{
            background:#08306b;
        }

        .error{
            color:red;
            text-align:center;
            margin-bottom:15px;
        }

        .signup{
            text-align:center;
            margin-top:15px;
        }

        .signup a{
            color:#0d47a1;
            text-decoration:none;
        }

        .password-wrapper{
            position:relative;
        }

        .eye{
            position:absolute;
            right:15px;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            user-select:none;
            font-size:18px;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>Login</h2>

    <?php if ($error): ?>
        <p class="error">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <input
            type="email"
            name="email"
            placeholder="Email Address"
            required
        >

        <div class="password-wrapper">

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Password"
                required
            >

            <span
                class="eye"
                id="eye"
                onclick="togglePassword()"
            >
                👁️
            </span>

        </div>

        <button type="submit">
            Login
        </button>

    </form>

    <div class="signup">
        Don't have an account?
        <a href="signup.php">Sign Up</a>
    </div>

</div>

<script>

function togglePassword() {

    const password = document.getElementById("password");
    const eye = document.getElementById("eye");

    if (password.type === "password") {

        password.type = "text";
        eye.textContent = "🙈";

    } else {

        password.type = "password";
        eye.textContent = "👁️";
    }
}

</script>

</body>
</html>
