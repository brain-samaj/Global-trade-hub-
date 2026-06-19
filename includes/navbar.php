<header class="header"><!-- HAMBURGER MENU -->
<button class="menu-btn" id="menuBtn">
    <i class="fas fa-bars"></i>
</button>

<!-- LOGO -->
<div class="logo">
    <div class="logo-globe">
        <span class="logo-text">GTH</span>
    </div>
</div>

<!-- DESKTOP NAVIGATION -->
<nav class="desktop-nav">
    <a href="index.php">Home</a>
    <a href="products.php">Products</a>
    <a href="about-us.php">About Us</a>
    <a href="contact.php">Contact Us</a>
</nav>

<!-- RIGHT ICONS -->
<div class="nav-icons">

    <button id="themeToggle" class="theme-toggle">
        🌙
    </button>

    <a href="cart.php" title="Cart">
        <i class="fas fa-shopping-cart"></i>
    </a>

    <a href="login.php" title="Login">
        <i class="fas fa-user-circle"></i>
    </a>

    <a href="about-us.php" title="About Us">
        <i class="fas fa-info-circle"></i>
    </a>

</div>

</header><!-- SIDEBAR --><div class="sidebar" id="sidebar"><div class="sidebar-top">

    <h2>MENU</h2>

    <button id="closeBtn">
        <i class="fas fa-times"></i>
    </button>

</div>

<a href="index.php">
    <i class="fas fa-home"></i> Home
</a>

<a href="products.php">
    <i class="fas fa-box-open"></i> Products
</a>

<a href="cart.php">
    <i class="fas fa-shopping-cart"></i> Cart
</a>

<a href="login.php">
    <i class="fas fa-user"></i> Login
</a>

<a href="signup.php">
    <i class="fas fa-user-plus"></i> Sign Up
</a>

<a href="become-seller.php">
    <i class="fas fa-store"></i> Become Seller
</a>

<a href="about-us.php">
    <i class="fas fa-info-circle"></i> About Us
</a>

<a href="contact.php">
    <i class="fas fa-envelope"></i> Contact Us
</a>

<a href="terms.php">
    <i class="fas fa-file-contract"></i> Terms & Conditions
</a>

</div><div class="overlay" id="overlay"></div><script>

const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("sidebar");
const closeBtn = document.getElementById("closeBtn");
const overlay = document.getElementById("overlay");

menuBtn.onclick = () => {
    sidebar.style.left = "0";
    overlay.style.display = "block";
};

closeBtn.onclick = () => {
    sidebar.style.left = "-320px";
    overlay.style.display = "none";
};

overlay.onclick = () => {
    sidebar.style.left = "-320px";
    overlay.style.display = "none";
};

</script>
