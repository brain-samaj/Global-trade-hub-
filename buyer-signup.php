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

        if ($name === "" || $email === "" || $phone === "" || $password === "") {
            throw new Exception("All fields are required");
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK IF USER EXISTS
        |--------------------------------------------------------------------------
        */

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            throw new Exception("Email already registered");
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE BUYER ACCOUNT
        |--------------------------------------------------------------------------
        */

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users
            (name, email, password, role, status)
            VALUES
            (:name, :email, :password, :role, :status)
        ");

        $stmt->execute([
            ":name" => $name,
            ":email" => $email,
            ":password" => $hashed,
            ":role" => "buyer",
            ":status" => "active"
        ]);

        /*
        |--------------------------------------------------------------------------
        | AUTO LOGIN (OPTIONAL)
        |--------------------------------------------------------------------------
        */

        $_SESSION["user_email"] = $email;
        $_SESSION["user_role"] = "buyer";

        header("Location: buyer-dashboard.php");
        exit;

    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buyer Signup</title>
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<h2>Create Buyer Account</h2>

<?php if ($message): ?>
    <p style="color:red;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<div style="background:white; padding:20px; max-width:400px; border-radius:10px;">

<form method="POST">

    <input type="text" name="name" placeholder="Full Name" required style="width:100%; padding:10px;">
    <br><br>

    <input type="email" name="email" placeholder="Email Address" required style="width:100%; padding:10px;">
    <br><br>

    <input type="text" name="phone" placeholder="Phone Number" required style="width:100%; padding:10px;">
    <br><br>

    <input type="password" name="password" placeholder="Password" required style="width:100%; padding:10px;">
    <br><br>

    <button type="submit"
        style="width:100%; padding:12px; background:#0d47a1; color:white; border:none;">
        Create Account
    </button>

</form>

</div>

</body>
</html>
