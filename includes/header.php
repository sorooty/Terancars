<!-- 
 * En-tête du site
 * Ce fichier contient la structure HTML de l'en-tête présent sur toutes les pages
 * Inclut les métadonnées, liens CSS, la barre de navigation et le logo
 -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Ajout de jQuery pour les fonctionnalités AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Meta tags pour améliorer le SEO -->
    <meta name="description" content="DaCar - Vente et location de véhicules de qualité. Trouvez votre voiture idéale parmi notre large sélection.">
    <meta name="keywords" content="voiture, automobile, achat voiture, location voiture, véhicules">
    <!-- Favicon -->
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/favicon.ico" type="image/x-icon">
    <!-- Styles personnalisés pour la charte graphique -->
    <style>
        :root {
            --primary-color: #042345;    /* Bleu foncé - couleur principale */
            --secondary-color: #B68FB2;  /* Violet - couleur secondaire */
            --white-color: #FFFFFF;      /* Blanc */
        }
        
        /* Styles spécifiques pour le header selon la charte graphique */
        header {
            background-color: var(--primary-color);
            color: var(--white-color);
        }
        
        .logo h1 span {
            color: var(--secondary-color);
        }
        
        .main-nav a {
            color: var(--white-color);
        }
        
        .main-nav a:hover {
            color: var(--secondary-color);
            background-color: rgba(182, 143, 178, 0.1); /* Violet transparent */
        }
        
        .btn-login, .btn-register {
            border: 1px solid var(--white-color);
            color: var(--white-color);
        }
        
        .btn-login:hover {
            background-color: var(--white-color);
            color: var(--primary-color);
        }
        
        .btn-register {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-register:hover {
            background-color: #c7a5c3; /* Version plus claire du violet */
        }
        
        /* Styles pour les alertes */
        .alert {
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 4px;
            position: relative;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container container">
            <div class="logo">
                <a href="<?php echo SITE_URL; ?>public/index.php">
                    <h1>Da<span>Car</span></h1>
                </a>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li><a href="<?php echo SITE_URL; ?>public/index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="<?php echo SITE_URL; ?>public/catalogue.php"><i class="fas fa-car"></i> Catalogue</a></li>
                    <li><a href="<?php echo SITE_URL; ?>public/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                </ul>
            </nav>
            
            <div class="user-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle">
                            <i class="fas fa-user-circle"></i> 
                            <?php 
                                $user = getCurrentUser();
                                echo $user ? htmlspecialchars($user['prenom']) : 'Mon compte'; 
                            ?>
                            <i class="fas fa-chevron-down"></i>
                        </a>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>public/profile.php"><i class="fas fa-user"></i> Mon profil</a>
                            <a href="<?php echo SITE_URL; ?>public/order-details.php"><i class="fas fa-shopping-bag"></i> Mes commandes</a>
                            <a href="<?php echo SITE_URL; ?>public/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>public/login.php" class="btn btn-login">Connexion</a>
                    <a href="<?php echo SITE_URL; ?>public/register.php" class="btn btn-register">Inscription</a>
                <?php endif; ?>
                
                <a href="<?php echo SITE_URL; ?>public/panier.php" class="cart-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cart-count">0</span>
                </a>
                
                <button class="mobile-menu-btn">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    
    <div class="main-content">
        <?php
        // Affichage des messages d'alerte
        $alert = getAlert();
        if ($alert) {
            echo '<div class="container">';
            echo '<div class="alert alert-' . $alert['type'] . '">';
            echo '<div class="alert-content">' . $alert['message'] . '</div>';
            echo '<button class="alert-close">&times;</button>';
            echo '</div>';
            echo '</div>';
        }
        ?>
        
        <!-- Script pour initialiser le compteur du panier -->
        <script>
            // Fonction pour mettre à jour le compteur du panier
            function updateCartCount() {
                // Récupérer le panier depuis le localStorage
                let cart = JSON.parse(localStorage.getItem('cart')) || [];
                
                // Calculer le nombre total d'articles
                let count = 0;
                cart.forEach(item => {
                    count += item.quantity;
                });
                
                // Mettre à jour l'affichage
                document.getElementById('cart-count').textContent = count;
            }
            
            // Mettre à jour le compteur au chargement de la page
            document.addEventListener('DOMContentLoaded', function() {
                updateCartCount();
                
                // Fermer les alertes
                const closeButtons = document.querySelectorAll('.alert-close');
                closeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        this.parentElement.style.display = 'none';
                    });
                });
                
                // Menu mobile
                const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
                const mainNav = document.querySelector('.main-nav');
                
                if (mobileMenuBtn) {
                    mobileMenuBtn.addEventListener('click', function() {
                        mainNav.classList.toggle('active');
                        this.classList.toggle('active');
                    });
                }
            });
            
            // Écouter les changements dans le localStorage
            window.addEventListener('storage', function(e) {
                if (e.key === 'cart') {
                    updateCartCount();
                }
            });
        </script>
