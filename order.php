<?php

session_start();

ob_start();
require __DIR__ . "/config/db.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid product ID");
}

$stmt = $pdo->prepare("
    SELECT *
    FROM products
    WHERE id = :id
");

$stmt->execute([
    ":id" => $id
]);

$product = $stmt->fetch();

if (!$product) {
    die("Product not found");
}

$product_price = (float)$product["price"];

$flutterwave_secret = getenv("FLUTTERWAVE_SECRET_KEY");

if (!$flutterwave_secret) {
    die("Flutterwave secret key not set");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);

    $delivery_address = trim($_POST["delivery_address"]);
    $state = trim($_POST["state"]);
    $city = trim($_POST["city"]);

    $delivery_type = $_POST["delivery_type"] ?? "standard";

    if (
        !$name ||
        !$email ||
        !$phone ||
        !$delivery_address ||
        !$state ||
        !$city
    ) {
        die("All fields are required");
    }

    if ($delivery_type === "express") {
        $delivery_fee = 3500;
    } else {
        $delivery_fee = 1500;
    }

    $total_amount = $product_price + $delivery_fee;

    $reference = uniqid("FLUTTERWAVE_");

    $seller_id = $product["seller_id"] ?? null;

    $platform_fee = $product_price * 0.10;
    $seller_earnings = $product_price - $platform_fee;

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
            seller_earnings,
            delivery_address,
            state,
            city,
            delivery_fee
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
            :seller_earnings,
            :delivery_address,
            :state,
            :city,
            :delivery_fee
        )
    ");

    $stmt->execute([
        ":product_id" => $id,
        ":seller_id" => $seller_id,
        ":user_id" => $_SESSION["user_id"],
        ":customer_name" => $name,
        ":email" => $email,
        ":phone" => $phone,
        ":amount" => $total_amount,
        ":reference" => $reference,
        ":platform_fee" => $platform_fee,
        ":seller_earnings" => $seller_earnings,
        ":delivery_address" => $delivery_address,
        ":state" => $state,
        ":city" => $city,
        ":delivery_fee" => $delivery_fee
    ]);

    $fields = [
        "tx_ref" => $reference,
        "amount" => $total_amount,
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

    curl_setopt($ch, CURLOPT_URL, "https://api.flutterwave.com/v3/payments");
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
    }

    die("Payment initialization failed");
}
?><!DOCTYPE html><html>
<head>
<title>Order Product</title><style>
body{
font-family:Arial;
max-width:700px;
margin:auto;
padding:20px;
}

input,textarea,select{
width:100%;
padding:10px;
margin-bottom:12px;
}

button{
background:green;
color:white;
border:none;
padding:12px;
width:100%;
cursor:pointer;
}

.total-box{
background:#f5f5f5;
padding:15px;
margin-bottom:15px;
border-radius:10px;
}
</style><script>
function updateFee(){

let type = document.getElementById("delivery_type").value;

let fee = 1500;

if(type === "express"){
fee = 3500;
}

let productPrice = <?= (int)$product_price ?>;

document.getElementById("delivery_fee").innerHTML =
"₦" + fee.toLocaleString();

document.getElementById("total_amount").innerHTML =
"₦" + (productPrice + fee).toLocaleString();
}
</script></head><body><h2><?= htmlspecialchars($product["name"]) ?></h2><p><?= htmlspecialchars($product["description"]) ?></p><div class="total-box"><p><b>Product Price:</b>
₦<?= number_format($product_price) ?></p><p><b>Delivery Fee:</b>
<span id="delivery_fee">₦1,500</span></p><p><b>Total:</b>
<span id="total_amount">
₦<?= number_format($product_price + 1500) ?>
</span></p></div><form method="POST"><input type="text"
name="name"
placeholder="Full Name"
required>

<input type="email"
name="email"
placeholder="Email"
required>

<input type="text"
name="phone"
placeholder="Phone Number"
required>

<textarea
name="delivery_address"
placeholder="Delivery Address"
required></textarea><input type="text"
name="state"
placeholder="State"
required>

<input type="text"
name="city"
placeholder="City"
required>

<select
name="delivery_type"
id="delivery_type"
onchange="updateFee()"
required>

<option value="standard">
Standard Delivery (₦2,500)
</option><option value="express">
Express Delivery (₦3,500)
</option></select><button type="submit">
Pay Now
</button></form></body>
</html>
