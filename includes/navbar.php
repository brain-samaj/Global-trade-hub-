<header class="header">

    <button class="menu-btn" id="menuBtn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="logo">
        <h2>GLOBAL TRADE HUB</h1>
    </div>

    <nav class="desktop-nav">
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="contact.php">Contact Us</a>
        <a href="about-us.php">About Us</a>
    </nav>

    <div class="nav-icons">
        <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
        <a href="login.php"><i class="fas fa-user"></i></a>
        <a href="about-us.php"><i class="fas fa-info-circle"></i> About Us
</a>
    </div>

</header>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <button id="closeBtn">X</button>

    <a href="index.php">Home</a>
    <a href="products.php">Products</a>
    <a href="cart.php">Cart</a>
    <a href="login.php">Login</a>
    <a href="terms.php">Terms & Conditions</a>
    <a href="become-seller.php">Become Seller</a>
    <a href="about-us.php">About Us</a>
    <a href="contact.php">Contact Us</a>


</div>

<div class="overlay" id="overlay"></div>

<script>
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const closeBtn = document.getElementById("closeBtn");
const overlay = document.getElementById("overlay");

menuBtn.onclick = () => {
    sidebar.style.left = "0";
    overlay.style.display = "block";
};

closeBtn.onclick = () => {
    sidebar.style.left = "-250px";
    overlay.style.display = "none";
};

overlay.onclick = () => {
    sidebar.style.left = "-250px";
    overlay.style.display = "none";
};
</script>
