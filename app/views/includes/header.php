<header class="header">
    <nav class="nav-main">
        <div class="logo">
            <a href="/DaCar/">
                <img src="/DaCar/public/images/logo.png" alt="TeranCar Logo" class="logo-img">
                <span class="logo-text">Teran<span class="highlight">Car</span></span>
            </a>
        </div>
        
        <div class="hamburger-menu">
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="nav-links">
            <a href="/DaCar/" <?php echo ($currentPage === 'home') ? 'class="active"' : ''; ?>>
                <i class="fas fa-home"></i> Accueil
            </a>
            <a href="/DaCar/vehicules" <?php echo ($currentPage === 'vehicules') ? 'class="active"' : ''; ?>>
                <i class="fas fa-car"></i> Véhicules
            </a>
            <a href="/DaCar/about" <?php echo ($currentPage === 'about') ? 'class="active"' : ''; ?>>
                <i class="fas fa-info-circle"></i> À propos
            </a>
            <a href="/DaCar/contact" <?php echo ($currentPage === 'contact') ? 'class="active"' : ''; ?>>
                <i class="fas fa-envelope"></i> Contact
            </a>
            <a href="/DaCar/panier" <?php echo ($currentPage === 'panier') ? 'class="active"' : ''; ?>>
                <i class="fas fa-shopping-cart"></i> Panier
                <?php if (isset($_SESSION['panier']) && count($_SESSION['panier']) > 0): ?>
                    <span class="cart-count"><?php echo count($_SESSION['panier']); ?></span>
                <?php endif; ?>
            </a>
        </div>

        <div class="nav-auth">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="/DaCar/profile" class="btn btn-outline">Mon Profil</a>
                <a href="/DaCar/logout" class="btn btn-secondary">Déconnexion</a>
            <?php else: ?>
                <a href="/DaCar/login" class="btn btn-outline">Connexion</a>
                <a href="/DaCar/register" class="btn btn-secondary">Inscription</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<!-- Ajout du script pour la navigation mobile -->
<script src="/DaCar/public/assets/js/nav.js"></script> 