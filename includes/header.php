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
    <meta name="description" content="DaCar - Vente et location de véhicules de qualité">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - DaCar' : 'DaCar - Vente & Location de Voitures'; ?></title>
    <link rel="stylesheet" href="/DaCar/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/DaCar/assets/images/favicon.png">
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <a href="/DaCar/public/index.php">
                    <h1>Da<span>Car</span></h1>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="/DaCar/public/index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="/DaCar/public/catalogue.php"><i class="fas fa-car"></i> Catalogue</a></li>
                    <li><a href="/DaCar/public/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="/DaCar/public/panier.php"><i class="fas fa-shopping-cart"></i> Panier
                        <?php if (isset($_SESSION['panier']) && (count($_SESSION['panier']['achat']) > 0 || count($_SESSION['panier']['location']) > 0)) { ?>
                            <span class="cart-count"><?php echo count($_SESSION['panier']['achat']) + count($_SESSION['panier']['location']); ?></span>
                        <?php } ?>
                    </a></li>
                </ul>
            </nav>
            <div class="user-actions">
                <?php if (isLoggedIn()) { ?>
                    <div class="dropdown">
                        <button class="btn btn-user dropdown-toggle">
                            <i class="fas fa-user-circle"></i> <?php echo $_SESSION['user_name']; ?>
                        </button>
                        <div class="dropdown-menu">
                            <a href="/DaCar/public/profile.php"><i class="fas fa-user"></i> Mon profil</a>
                            <a href="/DaCar/public/orders.php"><i class="fas fa-shopping-bag"></i> Mes commandes</a>
                            <?php if (isAdmin()) { ?>
                                <a href="/DaCar/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Administration</a>
                            <?php } ?>
                            <a href="/DaCar/public/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                        </div>
                    </div>
                <?php } else { ?>
                    <a href="/DaCar/public/login.php" class="btn btn-login"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                    <a href="/DaCar/public/register.php" class="btn btn-register"><i class="fas fa-user-plus"></i> Inscription</a>
                <?php } ?>
            </div>
            
            <!-- Bouton menu mobile -->
            <button class="mobile-menu-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>
    
    <!-- Affichage des messages d'alerte -->
    <?php 
    $alert = getAlert();
    if ($alert) { 
    ?>
        <div class="alert alert-<?php echo $alert['type']; ?>">
            <div class="alert-content">
                <?php echo $alert['message']; ?>
            </div>
            <button class="alert-close">&times;</button>
        </div>
    <?php } ?>
    
    <div class="main-content">
