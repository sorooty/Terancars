<!-- 
 * Pied de page
 * Ce fichier contient la structure HTML du pied de page présent sur toutes les pages
 * Inclut les liens de navigation secondaire, informations légales et réseaux sociaux
 -->

    </div><!-- Fin de .main-content -->
    
    <footer>
        <div class="footer-container container">
            <div class="footer-section">
                <h3>À propos de DaCar</h3>
                <p>DaCar est votre partenaire de confiance pour l'achat et la location de véhicules de qualité. Nous proposons une large gamme de voitures pour répondre à tous vos besoins.</p>
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
                    <li><i class="fas fa-envelope"></i> contact@dacar.fr</li>
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
                <p>&copy; <?php echo date('Y'); ?> DaCar. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
    
    <!-- Scripts JavaScript -->
    <script src="<?php echo SITE_URL; ?>assets/jscript/main.js"></script>
    
    <!-- Styles personnalisés pour le footer selon la charte graphique -->
    <style>
        footer {
            background-color: #042345; /* Bleu de la charte graphique */
            color: #FFFFFF;
            padding: 3rem 0 0;
        }
        
        .footer-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            padding-bottom: 2rem;
        }
        
        .footer-section h3 {
            color: #B68FB2; /* Violet de la charte graphique */
            margin-bottom: 1.2rem;
            font-size: 1.2rem;
        }
        
        .footer-section p {
            color: #e0e0e0;
            line-height: 1.6;
        }
        
        .footer-section ul {
            list-style: none;
        }
        
        .footer-section ul li {
            margin-bottom: 0.8rem;
        }
        
        .footer-section ul li a {
            color: #FFFFFF;
            transition: color 0.3s ease;
        }
        
        .footer-section ul li a:hover {
            color: #B68FB2; /* Violet de la charte graphique */
        }
        
        .social-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: #FFFFFF;
            transition: background-color 0.3s ease;
        }
        
        .social-icons a:hover {
            background-color: #B68FB2; /* Violet de la charte graphique */
        }
        
        .footer-bottom {
            background-color: rgba(0, 0, 0, 0.2);
            padding: 1.5rem 0;
            text-align: center;
        }
        
        .footer-bottom p {
            color: #e0e0e0;
            font-size: 0.9rem;
        }
        
        @media (max-width: 992px) {
            .footer-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 576px) {
            .footer-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html>
