<?php
require "config/db.php";
$flutterwave_secret = getenv("FLW_SECRET_KEY");
$flutterwave_public = getenv("FLW_PUBLIC_KEY");
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = $_POST["email"];

    if (!$email) {
        die("Email required");
    }

    $reference = uniqid("SELLER_FW_");

    // save pending payment
    $stmt = $pdo->prepare("
        INSERT INTO seller_payments (seller_email, amount, reference, status)
        VALUES (:email, :amount, :reference, 'pending')
    ");

    $stmt->execute([
        ":email" => $email,
        ":amount" => 20000,
        ":reference" => $reference
    ]);

    // Flutterwave redirect
    $payment_link = "https://checkout.flutterwave.com/v3/hosted/pay";

    $data = [
        "public_key" => $flutterwave_public,
        "tx_ref" => $reference,
        "amount" => 20000,
        "currency" => "NGN",
        "payment_options" => "card,banktransfer,ussd",
        "redirect_url" => "http://localhost:8000/seller-verify.php",
        "customer" => [
            "email" => $email
        ],
        "customizations" => [
            "title" => "Become a Seller",
            "description" => "Seller registration fee"
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

    echo "</form><script>document.getElementById('pay').submit();</script>";
}
?>

<h2>Become a Seller</h2>

<form method="POST">
    <input type="email" name="email" placeholder="Enter Email" required>
    <button>Pay ₦20,000</button>
</form>
