<?php
session_start();
require "config/db.php";

/*
|--------------------------------------------------------------------------
| AUTH CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "buyer") {
    exit("Access denied: Buyers only.");
}

$user_id = $_SESSION["user_id"];

/*
|--------------------------------------------------------------------------
| FETCH ORDERS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        o.*,
        p.name AS product_name,
        p.image_url
    FROM orders o
    LEFT JOIN products p ON o.product_id = p.id
    WHERE o.user_id = :user_id
    ORDER BY o.id DESC
");

$stmt->execute([":user_id" => $user_id]);

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| TRACKING FUNCTION
|--------------------------------------------------------------------------
*/

function stepClass($current, $step) {
    $steps = ["pending", "paid", "delivered"];

    $currentIndex = array_search($current, $steps);
    $stepIndex = array_search($step, $steps);

    if ($currentIndex === false || $stepIndex === false) {
        return "";
    }

    return $currentIndex >= $stepIndex ? "active" : "";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body{
            font-family: Arial;
            background:#f5f5f5;
            padding:20px;
        }

        .card{
            background:white;
            padding:15px;
            margin-bottom:20px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        img{
            width:100%;
            max-height:200px;
            object-fit:cover;
            border-radius:10px;
        }

        /* TRACKER */
        .tracker{
            display:flex;
            justify-content:space-between;
            margin:15px 0;
        }

        .step{
            flex:1;
            text-align:center;
            position:relative;
            font-size:12px;
        }

        .step::after{
            content:"";
            position:absolute;
            top:10px;
            right:-50%;
            width:100%;
            height:3px;
            background:#ddd;
            z-index:0;
        }

        .step:last-child::after{
            display:none;
        }

        .dot{
            width:20px;
            height:20px;
            border-radius:50%;
            background:#ccc;
            margin:0 auto 5px auto;
            position:relative;
            z-index:1;
        }

        .active .dot{
            background:green;
        }

        .active{
            font-weight:bold;
            color:green;
        }

        .status{
            display:inline-block;
            padding:5px 10px;
            border-radius:5px;
            color:white;
            font-size:12px;
        }

        .pending{background:orange;}
        .paid{background:blue;}
        .delivered{background:green;}
    </style>
</head>

<body>

<h2>📦 My Orders</h2>

<a href="buyer-dashboard.php">← Back</a>

<br><br>

<?php if (empty($orders)): ?>
    <div class="card">No orders found.</div>
<?php endif; ?>

<?php foreach ($orders as $o): ?>

<div class="card">

    <?php if (!empty($o["image_url"])): ?>
        <img src="<?= htmlspecialchars($o["image_url"]) ?>">
    <?php endif; ?>

    <h3><?= htmlspecialchars($o["product_name"]) ?></h3>

    <p><strong>Amount:</strong> ₦<?= number_format($o["amount"]) ?></p>

    <p><strong>Reference:</strong> <?= htmlspecialchars($o["reference"]) ?></p>

    <p>
        <span class="status <?= $o["status"] ?>">
            <?= strtoupper($o["status"]) ?>
        </span>
    </p>

    <!-- TRACKING -->
    <div class="tracker">

        <div class="step <?= stepClass($o["status"], "pending") ?>">
            <div class="dot"></div>
            Pending
        </div>

        <div class="step <?= stepClass($o["status"], "paid") ?>">
            <div class="dot"></div>
            Paid
        </div>

        <div class="step <?= stepClass($o["status"], "delivered") ?>">
            <div class="dot"></div>
            Delivered
        </div>

    </div>

    <p><small>Date: <?= $o["created_at"] ?></small></p>

</div>

<?php endforeach; ?>

</body>
</html>
