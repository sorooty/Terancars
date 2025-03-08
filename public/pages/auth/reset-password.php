<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

require_once '../../../config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/auth.css') ?>">
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
                <a href="<?= url('/') ?>">
                    <i class="fas fa-home"></i>
                    Accueil
                </a>
                <a href="<?= url('public/pages/catalogue/') ?>">
                    <i class="fas fa-car"></i>
                    Catalogue
                </a>
                <a href="<?= url('public/pages/contact/') ?>">
                    <i class="fas fa-envelope"></i>
                    Contact
                </a>
                <a href="<?= url('public/pages/about/') ?>">
                    <i class="fas fa-info-circle"></i>
                    À propos
                </a>
            </div>

            <div class="nav-auth">
                <a href="<?= url('auth/login') ?>" class="btn btn-outline">Connexion</a>
                <a href="<?= url('auth/inscription') ?>" class="btn btn-secondary">Inscription</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="auth-container">
            <h2>Mot de passe oublié</h2>
            <p>Vous allez recevoir un e-mail pour modifier votre mot de passe</p>
            
            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $email = clean_input($_POST["email"]);

                if (empty($email)) {
                    echo '<div class="auth-message error">Veuillez saisir votre adresse e-mail</div>';
                } else {
                    // TODO: Ajouter la logique d'envoi d'e-mail
                    echo '<div class="auth-message success">Un e-mail de réinitialisation a été envoyé à votre adresse</div>';
                }
            }
            ?>

            <form method="POST" action="" class="auth-form">
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" name="email" id="email" placeholder="votre@email.com" required>
                </div>

                <button type="submit" class="btn btn-primary">Envoyer le lien</button>

                <div class="auth-links">
                    <p><a href="<?= url('public/pages/auth/login.php') ?>">Retour à la connexion</a></p>
                </div>
            </form>
        </div>
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
                    <li><a href="<?= url('public/pages/catalogue/') ?>">Catalogue</a></li>
                    <li><a href="<?= url('public/pages/contact/') ?>">Contact</a></li>
                    <li><a href="<?= url('public/pages/about/') ?>">À propos</a></li>
                </ul>
            </div>
            
            <div class="footer-section contact">
                <h3>Contactez-nous</h3>
                <p>
                    <span><i class="fas fa-phone"></i> +33 1 23 45 67 89</span>
                    <span><i class="fas fa-envelope"></i> contact@terancars.fr</span>
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
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?> - Vente & Location de Voitures | Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>