<footer>
    <div id="footer-container">
        <div class="footer-row"><a class="return" href="#logo"><i class="fa solid fa-chevron-up fa-2xl" aria-hidden="true"></i></a></div>
        <div class="footer-row">
            <div class="footer-half-left">
                <a href="terms.html">Terms & Conditions</a></div>
            <div class="footer-half-right"></div>
        </div>
        <div class="footer-row">
            <div class="footer-half-left">
                <a href="https://www.facebook.com" target="_blank"><i class="fa fa-facebook fa-xl"></i></a>
                <a href="https://www.twitter.com" target="_blank"><i class="fa fa-twitter fa-xl"></i></a>
                <a href="https://www.instagram.com" target="_blank"><i class="fa fa-instagram fa-xl"></i></a>
            </div>
            <div class="footer-half-right">&copy; 2024 Andrea Ostruszkova</div>
        </div>
    </div>
</footer>
<script>
    // Smooth scroll to the top
    document.addEventListener('DOMContentLoaded', function() {
        const returnButton = document.querySelector('.return');

        returnButton.addEventListener('click', function(event) {
            event.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>
</body>
</html>
