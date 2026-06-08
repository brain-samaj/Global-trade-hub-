<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Global Trade Hub</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background: url('assets/bg.png') no-repeat center center/cover;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    text-align:center;
    color:white;
    position:relative;
}

/* dark overlay */
.overlay{
    position:absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:1;
}

.container{
    position:relative;
    z-index:2;
    animation: fadeIn 1s ease-in-out;
}

.logo{
    width:120px;
    height:120px;
    border-radius:50%;
    margin-bottom:20px;
}

h1{
    font-size:36px;
    margin-bottom:10px;
}

p{
    font-size:18px;
    margin-bottom:30px;
}

.loader{
    width:50px;
    height:50px;
    border:5px solid rgba(255,255,255,0.3);
    border-top:5px solid white;
    border-radius:50%;
    animation:spin 1s linear infinite;
    margin:auto;
}

@keyframes spin{
    100%{
        transform:rotate(360deg);
    }
}

@keyframes fadeIn{
    from{
        opacity:0;
    }
    to{
        opacity:1;
    }
}
</style>

<script>
setTimeout(function(){
    window.location.href = "index.php";
}, 4000);
</script>

</head>

<body>

<div class="overlay"></div>

<div class="container">

    <img src="assets/logo.png" class="logo" alt="Global Trade Hub Logo">

    <h1>Global Trade Hub</h1>

    <p>Connecting Buyers and Sellers Worldwide</p>

    <div class="loader"></div>

</div>

</body>
</html>
