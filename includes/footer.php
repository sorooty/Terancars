<!-- 
 * Pied de page
 * Ce fichier contient la structure HTML du pied de page présent sur toutes les pages
 * Inclut les liens de navigation secondaire, informations légales et réseaux sociaux
 -->

    </div><!-- Fin de .main-content -->
    
    <footer>
        <div class="footer-container container">
            <div class="footer-section">
                <h3>À propos de Terancar</h3>
                <p>Terancar est votre partenaire de confiance pour l'achat et la location de véhicules de qualité. Nous proposons une large gamme de voitures pour répondre à tous vos besoins.</p>
            </div>
            
            <div class="footer-section">
                <h3>Liens rapides</h3>
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>public/index.php">Accueil</a></li>
                    <li><a href="<?php echo SITE_URL; ?>public/catalogue.php">Catalogue</a></li>
                    <li><a href="<?php echo SITE_URL; ?>public/contact.php">Contact</a></li>
                    <li><a href="<?php echo SITE_URL; ?>public/test-connection.php">Test de connexion</a></li>
                    <li><a href="<?php echo SITE_URL; ?>public/api-test.php">Test d'API</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Nous contacter</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> 123 Avenue des Véhicules, 75000 Paris</li>
                    <li><i class="fas fa-phone"></i> +33 1 23 45 67 89</li>
                    <li><i class="fas fa-envelope"></i> contact@terancar.fr</li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Suivez-nous</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?php echo date('Y'); ?> Terancar. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts JavaScript - uniquement inclus sur la page d'accueil -->
    <?php if (isset($pageTitle) && $pageTitle === "Accueil"): ?>
        <script src="<?php echo SITE_URL; ?>assets/jscript/main.js"></script>
    <?php endif; ?>
</body>
</html>
