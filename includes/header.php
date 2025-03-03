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
    <!-- Meta tags pour améliorer le SEO -->
    <meta name="description" content="Terancar - Vente et location de véhicules de qualité. Trouvez votre voiture idéale parmi notre large sélection.">
    <meta name="keywords" content="voiture, automobile, achat voiture, location voiture, véhicules, Terancar">
    <!-- Favicon -->
    <link rel="icon" href="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png" type="image/png">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png">
    <!-- Meta tags pour les réseaux sociaux -->
    <meta property="og:title" content="<?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?>">
    <meta property="og:description" content="Terancar - Vente et location de véhicules de qualité. Trouvez votre voiture idéale parmi notre large sélection.">
    <meta property="og:image" content="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png">
    <meta property="og:url" content="<?php echo SITE_URL; ?>">
    <meta name="twitter:card" content="summary_large_image">
</head>
<body>
    <header>
        <div class="header-container container">
            <div class="logo">
                <a href="<?php echo SITE_URL; ?>public/index.php">
                    <div class="terancar-logo">
                        <img src="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png" alt="Terancar Logo">
                        <div class="text">
                            <span class="brand-name">Terancar</span>
                            <span class="tagline">VOTRE PARTENAIRE AUTO</span>
                        </div>
                    </div>
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
    </div>
</body>
</html>
