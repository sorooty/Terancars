<!-- Menu de navigation -->

<div class="navbar">
    <div class="container">
        <div class="navbar-content">
            <div class="logo-container">
                <a href="<?php echo SITE_URL; ?>" class="logo">
                    <img src="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png" alt="Terancar Logo">
                    <span class="brand-name">Terancar</span>
                </a>
            </div>
            
            <div class="nav-links">
                <a href="<?php echo SITE_URL; ?>" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Accueil</a>
                <a href="<?php echo SITE_URL; ?>catalogue.php" class="<?php echo $currentPage === 'catalogue.php' ? 'active' : ''; ?>">Catalogue</a>
                <a href="<?php echo SITE_URL; ?>contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>
            </div>
            
            <div class="nav-actions">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="dropdown-toggle">
                            <i class="fas fa-user-circle"></i>
                            <span>Mon compte</span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="<?php echo SITE_URL; ?>profile.php">
                                <i class="fas fa-user"></i> Profil
                            </a>
                            <a href="<?php echo SITE_URL; ?>order-details.php">
                                <i class="fas fa-history"></i> Mes commandes
                            </a>
                            <a href="<?php echo SITE_URL; ?>logout.php">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>login.php" class="nav-btn">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Connexion</span>
                    </a>
                <?php endif; ?>
                
                <a href="<?php echo SITE_URL; ?>panier.php" class="nav-btn cart-btn">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-counter">0</span>
                </a>
                
                <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mobile menu -->
<div class="mobile-menu">
    <div class="mobile-menu-header">
        <div class="logo-container">
            <a href="<?php echo SITE_URL; ?>" class="logo">
                <img src="<?php echo SITE_URL; ?>assets/images/logos/terancar-logo.png" alt="Terancar Logo">
                <span class="brand-name">Terancar</span>
            </a>
        </div>
        <button class="mobile-menu-close">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <div class="mobile-menu-content">
        <div class="mobile-nav-links">
            <a href="<?php echo SITE_URL; ?>" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Accueil</a>
            <a href="<?php echo SITE_URL; ?>catalogue.php" class="<?php echo $currentPage === 'catalogue.php' ? 'active' : ''; ?>">Catalogue</a>
            <a href="<?php echo SITE_URL; ?>contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a>
        </div>
        
        <div class="mobile-nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?php echo SITE_URL; ?>profile.php">
                    <i class="fas fa-user"></i> Mon profil
                </a>
                <a href="<?php echo SITE_URL; ?>order-details.php">
                    <i class="fas fa-history"></i> Mes commandes
                </a>
                <a href="<?php echo SITE_URL; ?>logout.php">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>login.php">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </a>
                <a href="<?php echo SITE_URL; ?>register.php">
                    <i class="fas fa-user-plus"></i> Inscription
                </a>
            <?php endif; ?>
            
            <a href="<?php echo SITE_URL; ?>panier.php">
                <i class="fas fa-shopping-cart"></i> Panier
            </a>
        </div>
    </div>
</div>