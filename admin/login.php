<?php
session_start();
require "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {

        if ($user["role"] !== "admin") {
            $error = "Access denied: Not an admin account";
        } else {

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["name"] = $user["name"];

            header("Location: dashboard.php");
            exit;
        }

    } else {
        $error = "Invalid email or password";
    }
}
?>

<h2>Admin Login</h2>

<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">

    <input name="email" type="email" placeholder="Email" required>
    <br><br>

    <input name="password" type="password" placeholder="Password" required>
    <br><br>

    <button type="submit">Login</button>

</form>
