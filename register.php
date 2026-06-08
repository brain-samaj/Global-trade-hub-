<?php
session_start();
require "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $password = $_POST["password"];
        $role = $_POST["role"]; // buyer or seller

        if ($name === "" || $email === "" || $password === "" || $role === "") {
            throw new Exception("All fields are required");
        }

        // check if email exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            throw new Exception("Email already exists");
        }

        // hash password
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$name, $email, $hashed, $role]);

        $message = "Account created successfully! You can now login.";

    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Global Trade Hub</title>
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<h2>Create Account</h2>

<?php if ($message): ?>
    <p style="padding:10px; background:#e0ffe0;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<div style="background:white; padding:20px; max-width:400px; border-radius:10px;">

<form method="POST">

    <input type="text" name="name" placeholder="Full Name" required style="width:100%; padding:10px;">
    <br><br>

    <input type="email" name="email" placeholder="Email" required style="width:100%; padding:10px;">
    <br><br>

    <input type="password" name="password" placeholder="Password" required style="width:100%; padding:10px;">
    <br><br>

    <select name="role" required style="width:100%; padding:10px;">
        <option value="">Select Role</option>
        <option value="buyer">Buyer</option>
        <option value="seller">Seller</option>
    </select>

    <br><br>

    <button type="submit"
        style="width:100%; padding:12px; background:green; color:white; border:none;">
        Sign Up
    </button>

</form>

</div>

</body>
</html>
