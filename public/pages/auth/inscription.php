<?php
// Inclusion du fichier d'initialisation
require_once ROOT_PATH . '/includes/init.php';

// Variables de la page
$pageTitle = "Inscription";
$pageDescription = "Inscrivez-vous sur " . SITE_NAME;
$currentPage = 'inscription';
$additionalCss = ['css/auth.css'];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean_input($_POST["name"]);
    $phone = clean_input($_POST["phone"]);
    $email = clean_input($_POST["email"]);
    $password = clean_input($_POST["password"]);

    if (empty($name) || empty($phone) || empty($email) || empty($password)) {
        $error_message = 'Tous les champs sont obligatoires';
    } else {
        // TODO: Ajouter la logique d'inscription
        $success_message = 'Inscription réussie !';
    }
}

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="auth-container">
    <h2>Inscription</h2>
    <?php if (isset($error_message)): ?>
        <div class="auth-message error"><?= $error_message ?></div>
    <?php endif; ?>
    <?php if (isset($success_message)): ?>
        <div class="auth-message success"><?= $success_message ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" class="auth-form">
        <div class="form-group">
            <label for="name">Nom Prénom*</label>
            <input type="text" name="name" id="name" placeholder="Votre nom complet" required>
        </div>

        <div class="form-group">
            <label for="phone">Numéro de téléphone*</label>
            <input type="tel" name="phone" id="phone" placeholder="+221" required>
        </div>

        <div class="form-group">
            <label for="email">Email*</label>
            <input type="email" name="email" id="email" placeholder="votre@email.com" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe*</label>
            <input type="password" name="password" id="password" placeholder="Votre mot de passe" required>
        </div>

        <div class="remember-me">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember">Se souvenir de moi</label>
        </div>
        
        <button type="submit" class="btn btn-primary">S'inscrire</button>

        <div class="auth-links">
            <p>Vous avez déjà un compte ? <a href="<?= url('pages/auth/login') ?>">Connectez-vous !</a></p>
        </div>
    </form>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?>
