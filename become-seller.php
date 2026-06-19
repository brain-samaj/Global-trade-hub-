<?php
require "config/db.php";

$flutterwave_secret = getenv("FLUTTERWAVE_SECRET_KEY");
$flutterwave_public = getenv("FLUTTERWAVE_PUBLIC_KEY");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if (!$email) {
        die("Email address is required.");
    }

    if (!isset($_POST["agree_terms"])) {
        die("You must accept the Terms & Conditions and Privacy Policy.");
    }

    $reference = uniqid("SELLER_FLUTTERWAVE_");

    // Save pending payment
    $stmt = $pdo->prepare("
        INSERT INTO seller_payments
        (seller_email, amount, reference, status)
        VALUES (:email, :amount, :reference, 'pending')
    ");

    $stmt->execute([
        ":email" => $email,
        ":amount" => 20000,
        ":reference" => $reference
    ]);

    // Flutterwave payment link
    $payment_link = "https://checkout.flutterwave.com/v3/hosted/pay";

    $data = [
        "public_key" => $flutterwave_public,
        "tx_ref" => $reference,
        "amount" => 20000,
        "currency" => "NGN",
        "payment_options" => "card,banktransfer,ussd",
        "redirect_url" => "https://global-trade-hub.com/flutterwave-callback.php",

        "customer" => [
            "email" => $email
        ],

        "customizations" => [
            "title" => "Global Trade Hub Seller Registration",
            "description" => "Seller registration payment"
        ]
    ];

    echo "<form id='pay' action='$payment_link' method='POST'>";

    foreach ($data as $key => $value) {

        if (is_array($value)) {

            foreach ($value as $k => $v) {
                echo "<input type='hidden' name='{$key}[{$k}]' value='$v'>";
            }

        } else {

            echo "<input type='hidden' name='$key' value='$value'>";
        }
    }

    echo "</form>";
    echo "<script>document.getElementById('pay').submit();</script>";

    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Become a Seller</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family:Arial; background:#f5f5f5; padding:20px;">

<div style="
    max-width:500px;
    margin:auto;
    background:white;
    padding:25px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,.1);
">

    <h2 style="text-align:center;">
        Become a Seller
    </h2>

    <p style="text-align:center;color:#666;">
        Seller Registration Fee: ₦20,000
    </p>

    <form method="POST">

        <input
            type="email"
            name="email"
            placeholder="Enter Email Address"
            required
            style="
                width:100%;
                padding:12px;
                margin-bottom:15px;
            "
        >

        <div style="
            margin:15px 0;
            padding:10px;
            background:#f8f9fa;
            border-radius:8px;
        ">

            <label style="line-height:1.8;">

                <input
                    type="checkbox"
                    name="agree_terms"
                    required
                >

                I agree to the

                <a href="terms.php" target="_blank">
                    Terms & Conditions
                </a>

                and

                <a href="privacy-policy.php" target="_blank">
                    Privacy Policy
                </a>

            </label>

        </div>

        <button
            type="submit"
            style="
                width:100%;
                padding:12px;
                background:#007bff;
                color:white;
                border:none;
                border-radius:8px;
                cursor:pointer;
                font-size:16px;
            "
        >
            Pay ₦20,000
        </button>

    </form>

</div>

</body>
</html>
