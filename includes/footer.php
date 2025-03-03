<!-- 
 * Pied de page
 * Ce fichier contient la structure HTML du pied de page présent sur toutes les pages
 * Inclut les liens de navigation secondaire, informations légales et réseaux sociaux
 -->

    </div><!-- Fin de .page-content -->
    
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>À propos de TeranCar</h3>
                    <p>Spécialiste de la vente et location de véhicules de qualité. Notre engagement : vous offrir le meilleur service possible.</p>
                </div>
                <div class="footer-section">
                    <h3>Liens rapides</h3>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="catalogue.php">Catalogue</a></li>
                        <li><a href="location.php">Location</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Rue du Commerce, 75001 Paris</p>
                    <p><i class="fas fa-phone"></i> +33 1 23 45 67 89</p>
                    <p><i class="fas fa-envelope"></i> contact@terancar.fr</p>
                </div>
                <div class="footer-section">
                    <h3>Suivez-nous</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> TeranCar. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts JavaScript - uniquement inclus sur la page d'accueil -->
    <?php if (isset($pageTitle) && $pageTitle === "Accueil"): ?>
        <script src="<?php echo SITE_URL; ?>assets/jscript/main.js"></script>
    <?php endif; ?>

    <script>
    // Menu mobile
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuBtn = document.createElement('button');
        mobileMenuBtn.className = 'mobile-menu-btn';
        mobileMenuBtn.innerHTML = '<span></span><span></span><span></span>';
        document.querySelector('.header-container').appendChild(mobileMenuBtn);

        const mainNav = document.querySelector('.main-nav');
        mobileMenuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.classList.toggle('active');
        });

        // Fermer le menu mobile lors du clic sur un lien
        const navLinks = document.querySelectorAll('.main-nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                mainNav.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
            });
        });

        // Animation des sections au scroll
        const animateOnScroll = function() {
            const elements = document.querySelectorAll('.vehicle-type, .widget, .brand-card, .review-card');
            
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementBottom = element.getBoundingClientRect().bottom;
                
                if (elementTop < window.innerHeight && elementBottom > 0) {
                    element.classList.add('animate');
                }
            });
        };

        // Ajouter la classe pour l'animation au chargement et au scroll
        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', animateOnScroll);

        // Smooth scroll pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });

    // Animation des alertes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    </script>
</body>
</html>
