<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign Up - Global Trade Hub</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    color:white;
}

.container{
    text-align:center;
    width:90%;
    max-width:500px;
}

h1{
    margin-bottom:10px;
}

p{
    margin-bottom:30px;
    opacity:0.9;
}

.card{
    background:white;
    color:black;
    padding:20px;
    margin:10px;
    border-radius:12px;
    cursor:pointer;
    transition:0.3s;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

.card:hover{
    transform:scale(1.05);
}

a{
    text-decoration:none;
    color:inherit;
}
</style>
</head>

<body>

<div class="container">

    <h1>Create Account</h1>
    <p>Choose how you want to join Global Trade Hub</p>

    <!-- BUYER -->
    <a href="buyer-signup.php">
        <div class="card">
            <h2>👤 Sign up as Buyer</h2>
            <p>Browse products and place orders easily</p>
        </div>
    </a>

    <!-- SELLER -->
    <a href="become-seller.php">
        <div class="card">
            <h2>🏪 Sign up as Seller</h2>
            <p>Sell products, manage store, earn money</p>
        </div>
    </a>

    <p style="margin-top:20px;">
        Already have an account?
        <a href="login.php" style="color:yellow;">Login</a>
    </p>

</div>

</body>
</html>
