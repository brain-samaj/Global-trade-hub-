<?php

ob_start();

require "config/db.php";

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
// PRICE PROCESSING
// ============================

$rawPrice = $product["price"];
$cleanPrice = preg_replace('/[^0-9]/', '', $rawPrice);
$amount = (int)$cleanPrice;

// ============================
// FLUTTERWAVE SECRET
// ============================

$flutterwave_secret = getenv("FLUTTERWAVE_SECRET_KEY");

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
    $reference = uniqid("FLW_");

    // Save order (pending)
    $stmt = $pdo->prepare("
        INSERT INTO orders (product_id, customer_name, phone, email, amount, reference)
        VALUES (:product_id, :customer_name, :phone, :email, :amount, :reference)
    ");

    $stmt->execute([
        ":product_id" => $id,
        ":customer_name" => $name,
        ":phone" => $phone,
        ":email" => $email,
        ":amount" => $amount,
        ":reference" => $reference
    ]);

    // ============================
    // FLUTTERWAVE PAYMENT INIT
    // ============================

    $url = "https://api.flutterwave.com/v3/payments";

    $fields = [
        "tx_ref" => $reference,
        "amount" => $amount,
        "currency" => "NGN",
        "redirect_url" => "http://localhost:8000/verify.php",
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

    $response = json_decode($result, true);

    // curl_close removed (fixes your warning)

    if (isset($response["status"]) && $response["status"] === "success") {
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

<b>₦<?= htmlspecialchars($product["price"]) ?></b>

<hr>

<h3>Checkout</h3>

<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="text" name="phone" placeholder="Phone" required><br><br>

    <button type="submit">Pay Now</button>
</form>

</body>
</html>
