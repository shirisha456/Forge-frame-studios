    </main>
    <footer class="site-footer">
        <div class="container">
            <div class="row g-4 py-5">
                <div class="col-lg-4">
                    <h3 class="footer-brand"><?php echo htmlspecialchars(SITE_NAME); ?></h3>
                    <p class="footer-tagline"><?php echo htmlspecialchars(SITE_TAGLINE); ?></p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                <div class="col-6 col-lg-2">
                    <h4 class="footer-heading">Site map</h4>
                    <ul class="footer-links">
                        <li><a href="/">Home</a></li>
                        <li><a href="/about.php">About</a></li>
                        <li><a href="/products.php">Services</a></li>
                        <li><a href="/news.php">News</a></li>
                        <li><a href="/contact.php">Contacts</a></li>
                        <li><a href="/users.php">User</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-2">
                    <h4 class="footer-heading">Services</h4>
                    <ul class="footer-links">
                        <li><a href="/product.php?slug=brand-anthem-film">Brand Anthem Film</a></li>
                        <li><a href="/product.php?slug=documentary-shorts">Documentary Shorts</a></li>
                        <li><a href="/product.php?slug=event-videography">Events</a></li>
                        <li><a href="/product.php?slug=drone-videography">Drone</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h4 class="footer-heading">Newsletter</h4>
                    <p class="small text-muted">Stay in the loop. <a href="mailto:hello@<?php echo htmlspecialchars(SITE_DOMAIN); ?>?subject=Newsletter%20signup">Email us</a> to join our list.</p>
                </div>
            </div>
            <div class="footer-bottom py-3">
                <p class="mb-0 small">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars(SITE_NAME); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_JS_COOKIES; ?>?v=2"></script>
    <script src="<?php echo ASSETS_JS_MAIN; ?>?v=1"></script>
    <?php if (isset($footer_scripts)) { echo $footer_scripts; } ?>
</body>
</html>
