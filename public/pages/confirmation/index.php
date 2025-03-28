<?php
// Inclusion du fichier d'initialisation
require_once __DIR__ . '/../../../includes/init.php';

// Variables de la page
$pageTitle = 'Confirmation de commande';
$pageDescription = 'Confirmation de votre commande chez Teran\'Cars';
$currentPage = 'confirmation';
$additionalCss = ['css/confirmation.css'];

// Début de la mise en mémoire tampon
ob_start();
?>

<div class="container">
    <div class="confirmation-container">
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>Commande confirmée</h1>
            <p class="confirmation-message">
                Merci pour votre commande ! Nous avons bien reçu votre paiement.
            </p>
            
            <div class="confirmation-details">
                <p>Un email de confirmation vous sera envoyé dans les prochaines minutes avec les détails de votre commande.</p>
                <p>Pour toute question, n'hésitez pas à contacter notre service client.</p>
            </div>
            
            <div class="confirmation-actions">
                <a href="<?= url('catalogue') ?>" class="btn btn-outline">
                    <i class="fas fa-car"></i> Retour au catalogue
                </a>
                <a href="<?= url('contact') ?>" class="btn btn-primary">
                    <i class="fas fa-envelope"></i> Nous contacter
                </a>
            </div>
        </div>
    </div>
</div>

<?php
// Récupération du contenu mis en mémoire tampon
$pageContent = ob_get_clean();

// Inclusion du template
require_once ROOT_PATH . '/includes/template.php';
?> 