<?php
/**
 * Footer complet pour TeranCar
 * Ce fichier contient la structure HTML du pied de page et les styles CSS associés
 * Il peut être partagé et réutilisé facilement
 */
?>

</div><!-- Fermeture de .main-content ouvert dans header.php -->

<footer>
    <div class="footer-content">
        <div class="footer-section about">
            <h3>À propos de TeranCar</h3>
            <p>TeranCar est votre partenaire de confiance pour l'achat et la location de véhicules de qualité depuis 2020.</p>
        </div>
        
        <div class="footer-section links">
            <h3>Liens rapides</h3>
            <ul>
                <li><a href="/TeranCar/public/index.php">Accueil</a></li>
                <li><a href="/TeranCar/public/catalogue.php">Catalogue</a></li>
                <li><a href="/TeranCar/public/contact.php">Contact</a></li>
            </ul>
        </div>
        
        <div class="footer-section contact">
            <h3>Contactez-nous</h3>
            <p>
                <span><i class="fas fa-phone"></i> +33 1 23 45 67 89</span>
                <span><i class="fas fa-envelope"></i> contact@terancar.fr</span>
                <span><i class="fas fa-map-marker-alt"></i> 123 Avenue des Véhicules, 75000 Paris</span>
            </p>
        </div>
        
        <div class="footer-section social">
            <h3>Suivez-nous</h3>
            <div class="socials">
                <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> TeranCar - Vente & Location de Voitures | Tous droits réservés.</p>
    </div>
</footer>

<style>
/* Styles pour le footer */
footer {
    background-color: #1a1a1a;
    color: #ffffff;
    padding: 3rem 0 0 0;
    margin-top: 4rem;
    position: relative;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2rem;
}

.footer-section {
    margin-bottom: 2rem;
}

.footer-section h3 {
    color: #ffffff;
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background-color: #4a6cf7;
}

.footer-section.about p {
    color: #cccccc;
    line-height: 1.6;
}

.footer-section.links ul {
    list-style: none;
    padding: 0;
}

.footer-section.links ul li {
    margin-bottom: 0.8rem;
}

.footer-section.links ul li a {
    color: #cccccc;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section.links ul li a:hover {
    color: #4a6cf7;
}

.footer-section.contact p {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.footer-section.contact span {
    display: flex;
    align-items: center;
    color: #cccccc;
}

.footer-section.contact i {
    margin-right: 0.5rem;
    color: #4a6cf7;
}

.footer-section.social .socials {
    display: flex;
    gap: 1rem;
}

.footer-section.social .socials a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: #ffffff;
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer-section.social .socials a:hover {
    background-color: #4a6cf7;
    transform: translateY(-3px);
}

.footer-bottom {
    background-color: #111111;
    padding: 1.5rem 0;
    text-align: center;
    margin-top: 2rem;
}

.footer-bottom p {
    color: #cccccc;
    margin: 0;
    font-size: 0.9rem;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .footer-content {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
    }
    
    .footer-section {
        text-align: center;
    }
    
    .footer-section h3::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .footer-section.contact span {
        justify-content: center;
    }
    
    .footer-section.social .socials {
        justify-content: center;
    }
}

/* Animation pour les liens sociaux */
@keyframes socialHover {
    0% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(0);
    }
}

.footer-section.social .socials a:hover i {
    animation: socialHover 0.3s ease;
}
</style>

<!-- Scripts JavaScript -->
<script src="/TeranCar/assets/jscript/main.js"></script>
</body>
</html> 