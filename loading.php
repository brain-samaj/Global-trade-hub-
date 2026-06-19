<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loading Global Trade Hub</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/style.css">

    <!-- Font Awesome (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<!-- 🌍 LOADING SCENE -->
<div class="hero-background">

    <!-- 🌍 GLOBE -->
    <div class="globe-3d">
        <div class="globe-core"></div>
        <div class="globe-grid"></div>
    </div>

    <!-- 🔗 TRADE ROUTES -->
    <div class="trade-routes">
        <div class="route route1"></div>
        <div class="route route2"></div>
        <div class="route route3"></div>
        <div class="route route4"></div>
    </div>

    <!-- 🚢 SHIPS -->
    <img src="assets/images/ship.png" class="ship">
    <img src="assets/images/ship.png" class="ship ship2">

</div>

<!-- ⏳ LOADING TEXT -->
<div class="loading-text">
    <h2>Loading Global Trade Hub...</h2>
    <p>Connecting global buyers and sellers...</p>
</div>

<!-- 🔁 AUTO REDIRECT -->
<script>
    setTimeout(function () {
        window.location.replace("index.php");
    }, 6000);
</script>

</body>
</html>
