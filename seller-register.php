<?php
session_start();
require "config/db.php";

$ref = $_GET["ref"] ?? null;

if (!$ref) {
    die("Invalid access");
}

/*
|--------------------------------------------------------------------------
| VERIFY PAYMENT
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT * FROM seller_payments
    WHERE reference = :ref AND status = 'paid'
");

$stmt->execute([":ref" => $ref]);
$payment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$payment) {
    die("Payment not verified");
}

$message = "";

/*
|--------------------------------------------------------------------------
| HANDLE REGISTRATION
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $full_name = trim($_POST["full_name"] ?? "");
        $phone     = trim($_POST["phone"] ?? "");
        $location  = trim($_POST["location"] ?? "");
        $nin       = trim($_POST["nin"] ?? "");

        if ($full_name === "" || $phone === "" || $location === "" || $nin === "") {
            throw new Exception("All fields are required");
        }

        $email = $payment["seller_email"];

        /*
        |--------------------------------------------------------------------------
        | CHECK IF USER ALREADY EXISTS
        |--------------------------------------------------------------------------
        */

        $check = $pdo->prepare("
            SELECT id FROM users WHERE email = :email LIMIT 1
        ");

        $check->execute([":email" => $email]);

        if ($check->fetch()) {
            throw new Exception("Account already exists");
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE SELLER USER (NO status COLUMN)
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (:name, :email, :password, :role)
        ");

        $stmt->execute([
            ":name" => $full_name,
            ":email" => $email,
            ":password" => password_hash("temp123", PASSWORD_BCRYPT),
            ":role" => "seller"
        ]);

        /*
        |--------------------------------------------------------------------------
        | REDIRECT TO PASSWORD SETUP
        |--------------------------------------------------------------------------
        */

        header("Location: seller-password.php?email=" . urlencode($email));
        exit;

    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Seller Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<h2>Seller Registration</h2>

<?php if ($message): ?>
    <p style="color:red;">
        <?= htmlspecialchars($message) ?>
    </p>
<?php endif; ?>

<div style="background:white; padding:20px; max-width:400px; margin:auto; border-radius:10px;">

<form method="POST">

    <input type="text" name="full_name" placeholder="Full Name" required>
    <br><br>

    <input type="text" name="phone" placeholder="Phone Number" required>
    <br><br>

    <input type="text" name="location" placeholder="Location" required>
    <br><br>

    <input type="text" name="nin" placeholder="NIN Number" required>
    <br><br>

    <button type="submit"
        style="width:100%; padding:12px; background:#0d47a1; color:white; border:none; cursor:pointer;">
        Continue
    </button>

</form>

</div>

</body>
</html>
