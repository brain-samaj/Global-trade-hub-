<?php
session_start();
?><!DOCTYPE html><html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Loading Global Trade Hub</title><style>

body{
    margin:0;
    overflow:hidden;
    background:#031428;
    font-family:Arial,sans-serif;
}

/* Background */

.loading-scene{
    position:fixed;
    inset:0;
    background:
    radial-gradient(circle at center,
    rgba(0,229,255,.08),
    transparent 60%),
    #031428;
}

/* Globe */

.globe{
    position:absolute;
    width:340px;
    height:340px;
    left:50%;
    top:50%;
    transform:translate(-50%,-50%);
    border-radius:50%;

    background:
    radial-gradient(circle at 30% 30%,
    #4efeff,
    transparent 30%),

    radial-gradient(circle at 70% 40%,
    #20d9e0,
    transparent 35%),

    radial-gradient(circle,
    #18aeb4,
    #0c3949);

    box-shadow:
    0 0 100px rgba(0,229,255,.35),
    0 0 200px rgba(0,229,255,.15);

    animation:rotateGlobe 15s linear infinite;
}

.globe::before{
    content:"";
    position:absolute;
    inset:0;
    border-radius:50%;

    background:
    repeating-linear-gradient(
        0deg,
        rgba(255,255,255,.05),
        rgba(255,255,255,.05) 1px,
        transparent 1px,
        transparent 15px
    ),

    repeating-linear-gradient(
        90deg,
        rgba(255,255,255,.05),
        rgba(255,255,255,.05) 1px,
        transparent 1px,
        transparent 15px
    );
}

/* Trade Routes */

.route{
    position:absolute;
    background:#00e5ff;
    height:2px;
    box-shadow:0 0 15px #00e5ff;
}

.route1{
    width:180px;
    left:30%;
    top:45%;
    transform:rotate(20deg);
}

.route2{
    width:200px;
    right:28%;
    top:55%;
    transform:rotate(-25deg);
}

.route3{
    width:170px;
    left:40%;
    top:35%;
    transform:rotate(-50deg);
}

/* Ships */

.ship{
    position:absolute;
    width:140px;
    filter:
    drop-shadow(0 0 20px rgba(255,255,255,.4));
}

.ship1{
    bottom:100px;
    left:-180px;
    animation:sailRight 15s linear infinite;
}

.ship2{
    top:120px;
    right:-180px;
    animation:sailLeft 18s linear infinite;
}

/* Loading Text */

.loading-text{
    position:absolute;
    bottom:80px;
    width:100%;
    text-align:center;
    color:white;
}

.loading-text h2{
    font-size:32px;
    color:#00e5ff;
}

.loading-text p{
    margin-top:10px;
    opacity:.8;
}

/* Animations */

@keyframes rotateGlobe{
    from{
        transform:translate(-50%,-50%) rotate(0deg);
    }
    to{
        transform:translate(-50%,-50%) rotate(360deg);
    }
}

@keyframes sailRight{
    from{
        left:-180px;
    }
    to{
        left:110%;
    }
}

@keyframes sailLeft{
    from{
        right:-180px;
    }
    to{
        right:110%;
    }
}

/* Mobile */

@media(max-width:768px){

    .globe{
        width:260px;
        height:260px;
    }

    .ship{
        width:110px;
    }

    .loading-text h2{
        font-size:24px;
    }
}

</style></head>
<body><div class="loading-scene"><div class="globe"></div>

<div class="route route1"></div>
<div class="route route2"></div>
<div class="route route3"></div>

<img src="assets/images/ship.png" class="ship ship1">
<img src="assets/images/ship.png" class="ship ship2">

<div class="loading-text">
    <h2>Global Trade Hub</h2>
    <p>Connecting Buyers & Sellers Worldwide</p>
</div>

</div><script>
setTimeout(function(){
    window.location.href="index.php";
},5000);
</script></body>
</html>
