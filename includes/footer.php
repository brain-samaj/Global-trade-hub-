<footer class="gth-footer">

    <div class="footer-stats">

        <div class="footer-stat">
            <i class="fas fa-users"></i>
            <h2>10,000+</h2>
            <p>Verified Sellers</p>
        </div>

        <div class="footer-stat">
            <i class="fas fa-box-open"></i>
            <h2>50,000+</h2>
            <p>Products Listed</p>
        </div>

        <div class="footer-stat">
            <i class="fas fa-globe"></i>
            <h2>150+</h2>
            <p>Countries Connected</p>
        </div>

        <div class="footer-stat">
            <i class="fas fa-shield-alt"></i>
            <h2>100%</h2>
            <p>Secure Transactions</p>
        </div>

    </div>

    <p class="copyright">
        © <?= date("Y") ?> Global Trade Hub. All Rights Reserved.
    </p>

</footer>

<script>

const themeBtn = document.getElementById("themeToggle");

/* Load saved theme */
if(localStorage.getItem("theme") === "dark"){

    document.body.classList.add("dark-mode");

    if(themeBtn){
        themeBtn.innerHTML = "☀️";
    }

}else{

    if(themeBtn){
        themeBtn.innerHTML = "🌙";
    }

}

/* Toggle theme */
if(themeBtn){

    themeBtn.addEventListener("click", function(){

        document.body.classList.toggle("dark-mode");

        if(document.body.classList.contains("dark-mode")){

            localStorage.setItem("theme","dark");
            themeBtn.innerHTML = "☀️";

        }else{

            localStorage.setItem("theme","light");
            themeBtn.innerHTML = "🌙";

        }

    });

}

</script>

</body>
</html>
