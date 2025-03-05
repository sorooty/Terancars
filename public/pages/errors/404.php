<?php
require_once '../../../includes/init.php';

// Variables de la page
$pageTitle = "Page non trouvée";
$pageDescription = "La page que vous recherchez n'existe pas";
$currentPage = 'error';
$additionalCss = ['css/errors.css'];

// Début de la mise en mémoire tampon
ob_start();
?>

<section class="error-container">
    <div class="container">
        <div class="error-content">
            <h1>404</h1>
            <h2>Page non trouvée</h2>
            <p>La page que vous recherchez n'existe pas ou a été déplacée.</p>
            <div class="error-actions">
                <a href="<?= url('/') ?>" class="btn btn-primary">
                    <i class="fas fa-home"></i> Retour à l'accueil
                </a>
                <a href="<?= url('pages/contact/') ?>" class="btn btn-outline">
                    <i class="fas fa-envelope"></i> Nous contacter
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 