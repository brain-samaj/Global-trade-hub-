<?php

ob_start();
require __DIR__ . "/config/db.php";

// ============================
// GET PRODUCT
// ============================

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid product ID");
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([":id" => $id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product not found");
}

// ============================
// PRICE HANDLING
// ============================

$amount = (float)$product["price"];

// ============================
// FLUTTERWAVE SECRET
// ============================

$flutterwave_secret = getenv("FLUTTERWAVE_SECRET_KEY");
$flutterwave_public = getenv("FLUTTERWAVE_PUBLIC_KEY");
if (!$flutterwave_secret) {
    die("Flutterwave secret key not set");
}

// ============================
// FORM SUBMISSION
// ============================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);

    if (!$name || !$email || !$phone) {
        die("All fields are required");
    }

    // Generate reference
    $reference = uniqid("FLUTTERWAVE_");

    // ============================
    // SELLER SUPPORT (PHASE 4.2)
    // ============================

    $seller_id = $product["seller_id"] ?? null;
    $platform_fee = $amount * 0.10;
    $seller_earnings = $amount - $platform_fee;

    // ============================
    // SAVE ORDER (PENDING)
    // ============================

$stmt = $pdo->prepare("
INSERT INTO orders
(
product_id,
seller_id,
user_id,
customer_name,
email,
phone,
amount,
reference,
status,
platform_fee,
seller_earnings
)
VALUES
(
:product_id,
:seller_id,
:user_id,
:customer_name,
:email,
:phone,
:amount,
:reference,
'pending',
:platform_fee,
:seller_earnings
)
");

$stmt->execute([
":product_id" => $id,
":seller_id" => $seller_id,
":user_id" => $_SESSION["user_id"],
":customer_name" => $name,
":email" => $email,
":phone" => $phone,
":amount" => $amount,
":reference" => $reference,
":platform_fee" => $platform_fee,
":seller_earnings" => $seller_earnings
]);

    // ============================
    // FLUTTERWAVE PAYMENT INIT
    // ============================

    $url = "https://api.flutterwave.com/v3/payments";

    $fields = [
        "tx_ref" => $reference,
        "amount" => $amount,
        "currency" => "NGN",
        "redirect_url" => "https://global-trade-hub-3nbz.onrender.com/verify.php",

        "customer" => [
            "email" => $email,
            "name" => $name,
            "phonenumber" => $phone
        ],

        "customizations" => [
            "title" => "Global Trade Hub",
            "description" => $product["name"]
        ]
    ];

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $flutterwave_secret,
        "Content-Type: application/json"
    ]);

    $result = curl_exec($ch);

    if ($result === false) {
        die("Curl Error: " . curl_error($ch));
    }

    curl_close($ch);

    $response = json_decode($result, true);

    if (isset($response["data"]["link"])) {
        header("Location: " . $response["data"]["link"]);
        exit;
    } else {
        die("Payment initialization failed");
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Product</title>
</head>
<body>

<h2><?= htmlspecialchars($product["name"]) ?></h2>

<p><?= htmlspecialchars($product["description"]) ?></p>

<b>₦<?= number_format($amount) ?></b>

<hr>

<h3>Checkout</h3>

<form method="POST">

    <input type="text" name="name" placeholder="Full Name" required><br><br>

    <input type="email" name="email" placeholder="Email" required><br><br>

    <input type="text" name="phone" placeholder="Phone Number" required><br><br>

    <button type="submit">Pay Now</button>

</form>

</body>
</html>
