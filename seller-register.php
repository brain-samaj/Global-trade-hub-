<?php
require "config/db.php";

$ref = $_GET["ref"] ?? null;

if (!$ref) {
    die("Invalid access");
}

// check payment is valid
$stmt = $pdo->prepare("
    SELECT * FROM seller_payments
    WHERE reference = :ref AND status = 'paid'
");
$stmt->execute([":ref" => $ref]);
$payment = $stmt->fetch();

if (!$payment) {
    die("Payment not verified");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
$payment = $stmt->fetch();

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

        $full_name = trim($_POST["full_name"]);
        $phone = trim($_POST["phone"]);
        $location = trim($_POST["location"]);
        $nin = trim($_POST["nin"]);

        if ($full_name === "" || $phone === "" || $location === "" || $nin === "") {
            throw new Exception("All fields are required");
        }

        $email = $payment["seller_email"];

        /*
        |--------------------------------------------------------------------------
        | CHECK IF USER EXISTS
        |--------------------------------------------------------------------------
        */

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->fetch()) {
            throw new Exception("Account already exists");
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE SELLER USER (PENDING)
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO users
            (name, email, password, role, status)
            VALUES
            (:name, :email, :password, :role, :status)
        ");

        $stmt->execute([
            ":name" => $full_name,
            ":email" => $email,
            ":password" => password_hash("temp12345", PASSWORD_DEFAULT),
            ":role" => "seller",
            ":status" => "pending"
        ]);

        /*
        |--------------------------------------------------------------------------
        | STORE EXTRA VERIFICATION DATA (OPTIONAL EXTENSION)
        |--------------------------------------------------------------------------
        | If you want, we can later move this into a seller_profiles table
        */

        $message = "Registration successful! Proceed to set password.";

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
</head>

<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<h2>Seller Registration</h2>

<?php if ($message): ?>
    <p style="color:red;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<div style="background:white; padding:20px; max-width:400px; border-radius:10px;">

<form method="POST">

    <input type="text" name="full_name" placeholder="Full Name" required style="width:100%; padding:10px;">
    <br><br>

    <input type="text" name="phone" placeholder="Phone Number" required style="width:100%; padding:10px;">
    <br><br>

    <input type="text" name="location" placeholder="Business Location" required style="width:100%; padding:10px;">
    <br><br>

    <input type="text" name="nin" placeholder="NIN Verification" required style="width:100%; padding:10px;">
    <br><br>

    <button type="submit"
        style="width:100%; padding:12px; background:green; color:white; border:none;">
        Continue
    </button>

</form>

</div>

</body>
</html>
