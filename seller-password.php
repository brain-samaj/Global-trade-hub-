<?php
session_start();
require "config/db.php";

$email = $_GET["email"] ?? null;

if (!$email) {
    die("Invalid request");
}

/*
|--------------------------------------------|
| VERIFY SELLER EXISTS
|--------------------------------------------|
*/
$stmt = $pdo->prepare("
    SELECT * FROM users
    WHERE email = ? AND role = 'seller'
");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    die("Seller account not found");
}

$message = "";

/*
|--------------------------------------------|
| HANDLE PASSWORD SETUP
|--------------------------------------------|
*/
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $password = $_POST["password"] ?? "";

        if ($password === "") {
            throw new Exception("Password is required");
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);

        /*
        |------------------------------------|
        | UPDATE ONLY PASSWORD (NO status)
        |------------------------------------|
        */
        $stmt = $pdo->prepare("
            UPDATE users
            SET password = :password
            WHERE email = :email AND role = 'seller'
        ");

        $stmt->execute([
            ":password" => $hashed,
            ":email" => $email
        ]);

        echo "<p style='color:green;'>Account activated successfully!</p>";
        echo "<a href='seller-login.php'>Go to Login</a>";
        exit;

    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Password</title>
</head>

<body style="font-family:Arial; background:#f5f5f5; text-align:center; padding:40px;">

<h2>Set Your Password</h2>

<?php if ($message): ?>
    <p style="color:red;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<div style="background:white; padding:20px; max-width:400px; margin:auto; border-radius:10px;">

<form method="POST">

    <input type="password" name="password"
        placeholder="Enter Password"
        style="width:100%; padding:10px; margin-bottom:15px;"
        required>

    <button type="submit"
        style="width:100%; padding:12px; background:green; color:white; border:none;">
        Activate Account
    </button>

</form>

</div>

</body>
</html>
