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

    $full_name = trim($_POST["full_name"]);
    $phone = trim($_POST["phone"]);
    $location = trim($_POST["location"]);
    $nin = trim($_POST["nin"]);
    $email = $payment["seller_email"];

    if (!$full_name || !$phone || !$location || !$nin) {
        die("All fields are required");
    }

    // save seller (NO PASSWORD YET)
    $stmt = $pdo->prepare("
        INSERT INTO sellers (full_name, email, phone, location, nin, status)
        VALUES (:full_name, :email, :phone, :location, :nin, 'pending')
    ");

    $stmt->execute([
        ":full_name" => $full_name,
        ":email" => $email,
        ":phone" => $phone,
        ":location" => $location,
        ":nin" => $nin
    ]);

    // go to password setup
    header("Location: seller-password.php?email=" . urlencode($email));
    exit;
}
?>

<h2>Seller Registration</h2>

<form method="POST">

    <input type="text" name="full_name" placeholder="Full Name" required><br><br>

    <input type="text" name="phone" placeholder="Phone" required><br><br>

    <input type="text" name="location" placeholder="Location" required><br><br>

    <input type="text" name="nin" placeholder="NIN (must match name)" required><br><br>

    <button type="submit">Continue</button>

</form>
