<?php
if (!defined('ROOT_PATH')) {
    require_once 'init.php';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($pageDescription) ? $pageDescription : SITE_NAME . ' - Votre partenaire de confiance pour l\'achat et la location de véhicules de qualité' ?>">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <?php if (isset($additionalCss) && is_array($additionalCss)): ?>
        <?php foreach ($additionalCss as $css): ?>
            <link rel="stylesheet" href="<?= asset($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <header class="header">
        <nav class="nav-main container">
            <div class="logo">
                <a href="<?= url('/') ?>">
                    <img src="<?= asset('images/icones/Tlogo.png') ?>" alt="<?= SITE_NAME ?> Logo" class="logo-img">
                    <span class="logo-text">Teran<span class="highlight">'Cars</span></span>
                </a>
            </div>
            
            <div class="nav-links">
                <a href="<?= url('/') ?>" class="<?= $currentPage === 'home' ? 'active' : '' ?>">
                    <i class="fas fa-home"></i>
                    Accueil
                </a>
                <a href="<?= url('pages/catalogue/') ?>" class="<?= $currentPage === 'catalogue' ? 'active' : '' ?>">
                    <i class="fas fa-car"></i>
                    Catalogue
                </a>
                <a href="<?= url('pages/contact/') ?>" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i>
                    Contact
                </a>
                <a href="<?= url('pages/about/') ?>" class="<?= $currentPage === 'about' ? 'active' : '' ?>">
                    <i class="fas fa-info-circle"></i>
                    À propos
                </a>
                <a href="<?= url('pages/panier/') ?>" class="<?= $currentPage === 'panier' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart"></i>
                    Panier
                    <?php if (!empty($_SESSION['panier'])): ?>
                        <span class="cart-count"><?= count($_SESSION['panier']) ?></span>
                    <?php endif; ?>
                </a>
            </div>

            <div class="nav-auth">
                <a href="<?= url('pages/auth/login') ?>" class="btn btn-outline">Connexion</a>
                <a href="<?= url('pages/auth/inscription') ?>" class="btn btn-secondary">Inscription</a>
            </div>
        </nav>
    </header>

    <main>
        <?= $pageContent ?>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>À propos de <?= SITE_NAME ?></h3>
                <p><?= SITE_NAME ?> est votre partenaire de confiance pour l'achat et la location de véhicules de qualité depuis 2020.</p>
            </div>
            
            <div class="footer-section links">
                <h3>Liens rapides</h3>
                <ul>
                    <li><a href="<?= url('/') ?>">Accueil</a></li>
                    <li><a href="<?= url('pages/catalogue/') ?>">Catalogue</a></li>
                    <li><a href="<?= url('pages/contact/') ?>">Contact</a></li>
                    <li><a href="<?= url('pages/about/') ?>">À propos</a></li>
                </ul>
            </div>
            
            <div class="footer-section contact">
                <h3>Contactez-nous</h3>
                <p>
                    <span><i class="fas fa-phone"></i> +221 78 123 45 67 / +221 33 823 45 67</span>
                    <span><i class="fas fa-envelope"></i> contact@terancars.sn</span>
                    <span><i class="fas fa-map-marker-alt"></i> 97 Route de la Corniche Dakar, Sénégal</span>
                </p>
            </div>
            
            <div class="footer-section social">
                <h3>Suivez-nous</h3>
                <div class="socials">
                    <a href="https://www.facebook.com/Terancars" title="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.instagram.com/terancars_sn" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/terancars" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?> - Vente & Location de Voitures | Tous droits réservés.</p>
        </div>
    </footer>

    <script src="<?= asset('js/main.js') ?>"></script>
    <?php if (isset($additionalJs) && is_array($additionalJs)): ?>
        <?php foreach ($additionalJs as $js): ?>
            <script src="<?= asset($js) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html> 