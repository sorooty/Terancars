<!-- Navigation principale -->
<header>
    <div class="container header-container">
        <div class="terancar-logo">
            <img src="assets/images/logo.png" alt="TeranCar Logo">
            <div class="text">
                <span class="brand-name">TeranCar</span>
                <span class="tagline">Location & Vente</span>
            </div>
        </div>
        
        <nav class="main-nav">
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="catalogue.php"><i class="fas fa-car"></i> Catalogue</a></li>
                <li><a href="location.php"><i class="fas fa-key"></i> Location</a></li>
                <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="profil.php"><i class="fas fa-user"></i> Mon Profil</a></li>
                    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
                <?php endif; ?>
                <li>
                    <a href="panier.php" class="cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cart-count" class="cart-count"><?php echo isset($_SESSION['panier']['items']) ? count($_SESSION['panier']['items']) : 0; ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>