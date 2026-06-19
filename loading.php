<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading Global Trade Hub</title>

    <link rel="stylesheet" href="assets/style.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>

<div class="hero-background">

    <!-- Globe -->
    <div class="globe-3d">
        <div class="globe-core"></div>
        <div class="globe-grid"></div>
    </div>

    <!-- Trade Routes -->
    <div class="trade-routes">
        <div class="route route1"></div>
        <div class="route route2"></div>
        <div class="route route3"></div>
        <div class="route route4"></div>
    </div>

    <!-- Ships -->
    <img src="assets/images/ship.png"
         alt="Cargo Ship"
         class="ship ship1">

    <img src="assets/images/ship.png"
         alt="Cargo Ship"
         class="ship ship2">

</div>

<div class="loading-text">
    <h2>Loading Global Trade Hub...</h2>
    <p>Connecting global buyers and sellers...</p>
</div>

<script>
setTimeout(function() {
    window.location.href = "index.php";
}, 6000);
</script>

</body>
</html>
