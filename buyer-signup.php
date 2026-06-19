<?php
session_start();
require "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $phone = trim($_POST["phone"]);
        $password = $_POST["password"];

        if (!isset($_POST["agree_terms"])) {
            throw new Exception(
                "You must agree to the Terms & Conditions and Privacy Policy."
            );
        }

        if (
            $name === "" ||
            $email === "" ||
            $phone === "" ||
            $password === ""
        ) {
            throw new Exception("All fields are required.");
        }

        /*
        |------------------------------------------------
        | CHECK IF EMAIL EXISTS
        |------------------------------------------------
        */

        $check = $pdo->prepare("
            SELECT id
            FROM users
            WHERE email = :email
        ");

        $check->execute([
            ":email" => $email
        ]);

        if ($check->fetch()) {
            throw new Exception("Email already registered.");
        }

        /*
        |------------------------------------------------
        | CREATE ACCOUNT
        |------------------------------------------------
        */

        $hashed = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $stmt = $pdo->prepare("
            INSERT INTO users (
                name,
                email,
                password,
                role
            )
            VALUES (
                :name,
                :email,
                :password,
                :role
            )
        ");

        $stmt->execute([
            ":name" => $name,
            ":email" => $email,
            ":password" => $hashed,
            ":role" => "buyer"
        ]);

        /*
        |------------------------------------------------
        | AUTO LOGIN
        |------------------------------------------------
        */

        $user_id = $pdo->lastInsertId();

        $_SESSION["user_id"] = $user_id;
        $_SESSION["user_email"] = $email;
        $_SESSION["role"] = "buyer";

        header("Location: buyer-dashboard.php");
        exit();

    } catch (Exception $e) {

        $message = $e->getMessage();

    }

}
?><!DOCTYPE html><html>
<head><title>Buyer Signup</title>

<meta name="viewport"
      content="width=device-width, initial-scale=1">

</head><body style="
    font-family:Arial;
    background:#f5f5f5;
    padding:20px;
"><h2 style="text-align:center;">
    Create Buyer Account
</h2><?php if ($message): ?><p style="
    color:red;
    text-align:center;
">
    <?= htmlspecialchars($message) ?>
</p><?php endif; ?><div style="
    background:white;
    padding:20px;
    max-width:500px;
    margin:auto;
    border-radius:10px;
"><form method="POST"><input
    type="text"
    name="name"
    placeholder="Full Name"
    required
    style="width:100%;padding:12px;"
>

<br><br>

<input
    type="email"
    name="email"
    placeholder="Email Address"
    required
    style="width:100%;padding:12px;"
>

<br><br>

<input
    type="text"
    name="phone"
    placeholder="Phone Number"
    required
    style="width:100%;padding:12px;"
>

<br><br>

<input
    type="password"
    name="password"
    placeholder="Password"
    required
    style="width:100%;padding:12px;"
>

<br><br>

<div style="
    font-size:14px;
    line-height:1.6;
    margin-bottom:20px;
">

    <label>

        <input
            type="checkbox"
            name="agree_terms"
            required
        >

        I agree to the

        <a href="terms.php" target="_blank">
            Terms & Conditions
        </a>

        and

        <a href="privacy-policy.php" target="_blank">
            Privacy Policy
        </a>

    </label>

</div>

<button
    type="submit"
    style="
        width:100%;
        padding:12px;
        background:green;
        color:white;
        border:none;
        border-radius:5px;
        cursor:pointer;
    "
>
    Create Account
</button>

</form></div></body>
</html>
